<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('pgp')]
final class PgpCliTest extends CliE2ETestCase
{
    #[Test]
    public function list_keys_works_in_data_mode(): void
    {
        $exit = $this->runCli(['command' => 'pgp:list', '--json' => true]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
    }

    #[Test]
    public function upload_via_contents_round_trip_in_admin_mode(): void
    {
        if ($this->env('WSAPI_TEST_ADMIN_IDTOKEN') === null || $this->env('WSAPI_TEST_PGP_KEY') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_ADMIN_IDTOKEN and WSAPI_TEST_PGP_KEY to run admin-mode PGP tests.');
        }
        $exit = $this->runCli([
            'command'           => 'pgp:upload',
            '--mode'            => 'admin',
            '--idtoken'         => $this->env('WSAPI_TEST_ADMIN_IDTOKEN'),
            '--apikey'          => $this->env('WSAPI_TEST_APIKEY'),
            '--pgpkeycontents'  => $this->env('WSAPI_TEST_PGP_KEY'),
            '--pgpkeypurpose'   => $this->env('WSAPI_TEST_PGP_PURPOSE', 'authorization'),
            '--json'            => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
        $list = $this->runListAndDecode();
        $newest = end($list);
        if ($newest && !empty($newest['pgpKeyId'])) {
            $this->trackPgpKey((string)$newest['pgpKeyId']);
        }
    }

    #[Test]
    public function upload_via_pgpkeyfile_is_supported(): void
    {
        if ($this->env('WSAPI_TEST_ADMIN_IDTOKEN') === null || $this->env('WSAPI_TEST_PGP_KEY') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_ADMIN_IDTOKEN and WSAPI_TEST_PGP_KEY to run admin-mode PGP tests.');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'wscli-pgp-');
        file_put_contents($tmp, $this->env('WSAPI_TEST_PGP_KEY'));
        try {
            $exit = $this->runCli([
                'command'        => 'pgp:upload',
                '--mode'         => 'admin',
                '--idtoken'      => $this->env('WSAPI_TEST_ADMIN_IDTOKEN'),
                '--apikey'       => $this->env('WSAPI_TEST_APIKEY'),
                '--pgpkeyfile'   => $tmp,
                '--json'         => true,
            ]);
            self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
            $list = $this->runListAndDecode();
            $newest = end($list);
            if ($newest && !empty($newest['pgpKeyId'])) {
                $this->trackPgpKey((string)$newest['pgpKeyId']);
            }
        } finally {
            unlink($tmp);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function runListAndDecode(): array
    {
        $this->runCli(['command' => 'pgp:list', '--json' => true]);
        $decoded = json_decode($this->tester->getDisplay(), true) ?: [];
        return $decoded['PgpKeys'] ?? $decoded['pgpKeys'] ?? [];
    }
}
