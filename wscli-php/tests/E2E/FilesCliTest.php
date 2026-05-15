<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('files')]
final class FilesCliTest extends CliE2ETestCase
{
    private string $bank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bank = $this->env('WSAPI_TEST_BANK', 'nordea');
    }

    /**
     * Files endpoints need an enrolled bank certificate behind the scenes —
     * a fresh data-mode account without certs gets `01 / Internal server
     * processing side error` from the bank-side leg. We assert exit==SUCCESS
     * when the bank is reachable, and accept FAILURE with a structured error
     * payload otherwise. Either branch validates the CLI's request/response
     * shape; neither tests bank-side reachability.
     */
    private function assertListFilesContract(int $exit, string $display): bool
    {
        if ($exit === Command::SUCCESS) {
            return true;
        }
        self::assertSame(Command::FAILURE, $exit, 'files:list must exit 0 or 1, got ' . $exit);
        self::assertStringContainsString('"ResponseCode"', $display, 'CLI must surface the structured server error');
        return false;
    }

    #[Test]
    public function list_files_succeeds_with_minimal_args(): void
    {
        $exit = $this->runCli([
            'command' => 'files:list',
            '--bank'  => $this->bank,
            '--json'  => true,
        ]);
        $this->assertListFilesContract($exit, $this->tester->getDisplay());
    }

    #[Test]
    public function list_files_with_filetype_and_filestatus(): void
    {
        $exit = $this->runCli([
            'command'      => 'files:list',
            '--bank'       => $this->bank,
            '--filetype'   => $this->env('WSAPI_TEST_FILETYPE', 'pain.001.001.02'),
            '--filestatus' => 'ALL',
            '--json'       => true,
        ]);
        $this->assertListFilesContract($exit, $this->tester->getDisplay());
    }

    #[Test]
    public function upload_via_filecontents_then_download_then_delete(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_FILE_UPLOAD') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_FILE_UPLOAD=1 to exercise the upload/download/delete round-trip.');
        }
        $fileType = $this->env('WSAPI_TEST_FILETYPE', 'pain.001.001.02');
        $fileName = 'cli-upload-' . date('Ymd-His') . '.xml';
        $payload  = "<e2e probe " . bin2hex(random_bytes(8)) . ">";
        $contents = base64_encode($payload);

        if ($this->fileReferences($fileType) === null) {
            $this->markTestSkipped('files:list returns 400 — bank cert not enrolled for this account, skipping upload round-trip.');
        }
        $beforeRefs = $this->fileReferences($fileType) ?? [];

        $exit = $this->runCli([
            'command'        => 'files:upload',
            '--bank'         => $this->bank,
            '--filetype'     => $fileType,
            '--filename'     => $fileName,
            '--filecontents' => $contents,
            '--signatures'   => '',
            '--json'         => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, 'upload: ' . $this->tester->getDisplay());

        $afterRefs = $this->fileReferences($fileType) ?? [];
        $newRefs = array_values(array_diff($afterRefs, $beforeRefs));
        self::assertNotEmpty($newRefs, 'upload must produce a new fileReference visible in files:list');
        $ref = (string)$newRefs[0];
        $this->trackFile($this->bank, $fileType, $ref);

        $downloadTo = tempnam(sys_get_temp_dir(), 'wscli-dl-');
        $exit = $this->runCli([
            'command'         => 'files:download',
            '--bank'          => $this->bank,
            '--filetype'      => $fileType,
            '--filereference' => $ref,
            '--output-file'   => $downloadTo,
            '--json'          => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, 'download: ' . $this->tester->getDisplay());
        self::assertSame($payload, (string)file_get_contents($downloadTo), 'round-trip payload must match');
        unlink($downloadTo);
    }

    #[Test]
    public function upload_via_filepath_uses_local_file(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_FILE_UPLOAD') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_FILE_UPLOAD=1.');
        }
        $fileType = $this->env('WSAPI_TEST_FILETYPE', 'pain.001.001.02');
        $tmp = tempnam(sys_get_temp_dir(), 'wscli-fp-');
        $payload = "<e2e filepath probe>";
        file_put_contents($tmp, $payload);

        try {
            $beforeRefs = $this->fileReferences($fileType);
            if ($beforeRefs === null) {
                $this->markTestSkipped('files:list returns 400 — bank cert not enrolled.');
            }
            $exit = $this->runCli([
                'command'    => 'files:upload',
                '--bank'     => $this->bank,
                '--filetype' => $fileType,
                '--filename' => 'cli-upload-fp-' . date('Ymd-His') . '.xml',
                '--filepath' => $tmp,
                '--json'     => true,
            ]);
            self::assertSame(Command::SUCCESS, $exit, 'upload (filepath): ' . $this->tester->getDisplay());
            $afterRefs = $this->fileReferences($fileType) ?? [];
            $newRefs = array_values(array_diff($afterRefs, $beforeRefs));
            self::assertNotEmpty($newRefs);
            $this->trackFile($this->bank, $fileType, (string)$newRefs[0]);
        } finally {
            unlink($tmp);
        }
    }

    /** @return string[]|null returns null when listFiles fails with a structured 4xx (no bank cert). */
    private function fileReferences(string $fileType): ?array
    {
        $exit = $this->runCli([
            'command'    => 'files:list',
            '--bank'     => $this->bank,
            '--filetype' => $fileType,
            '--filestatus' => 'ALL',
            '--json'     => true,
        ]);
        if ($exit !== 0) {
            return null;
        }
        $decoded = json_decode($this->tester->getDisplay(), true) ?: [];
        $descriptors = $decoded['FileDescriptors']
            ?? $decoded['fileDescriptors']
            ?? [];
        return array_values(array_map(static fn ($d) => (string)($d['FileReference'] ?? $d['fileReference'] ?? ''), $descriptors));
    }

    #[Test]
    public function sync_writes_files_into_syncdir(): void
    {
        $dir = sys_get_temp_dir() . '/wscli-sync-' . uniqid();
        try {
            $exit = $this->runCli([
                'command'    => 'files:sync',
                '--bank'     => $this->bank,
                '--filetype' => $this->env('WSAPI_TEST_FILETYPE', 'pain.001.001.02'),
                '--syncdir'  => $dir,
                '--json'     => true,
            ]);
            // SUCCESS when bank backend is reachable; FAILURE with structured
            // server error when the account has no enrolled cert. Either is a
            // valid CLI contract pass.
            if ($exit !== Command::SUCCESS) {
                self::assertSame(Command::FAILURE, $exit, $this->tester->getDisplay());
                self::assertStringContainsString('"ResponseCode"', $this->tester->getDisplay());
                return;
            }
            self::assertDirectoryExists($dir);
        } finally {
            if (is_dir($dir)) {
                array_map('unlink', glob($dir . '/*') ?: []);
                @rmdir($dir);
            }
        }
    }
}
