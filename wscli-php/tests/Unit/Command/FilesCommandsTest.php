<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Files\DeleteFileCommand;
use IsecureFi\WsCli\Command\Files\DownloadFileCommand;
use IsecureFi\WsCli\Command\Files\DownloadFilesCommand;
use IsecureFi\WsCli\Command\Files\ListFilesCommand;
use IsecureFi\WsCli\Command\Files\UploadFileCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

final class FilesCommandsTest extends CommandTestCase
{
    private const AUTH = ['--idtoken' => 'ey.fake', '--apikey' => 'AB'];

    #[Test]
    public function list_files_requires_bank(): void
    {
        $t = $this->tester(new ListFilesCommand());
        $exit = $t->execute($this->withConfig(self::AUTH));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--bank is required', $t->getDisplay());
    }

    #[Test]
    public function upload_rejects_mutually_exclusive_filepath_and_filecontents(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'wscli-upload-');
        file_put_contents($tmp, 'hello');
        try {
            $t = $this->tester(new UploadFileCommand());
            $exit = $t->execute($this->withConfig(array_merge(self::AUTH, [
                '--bank' => 'nordea', '--filetype' => 'KTL', '--filename' => 'x',
                '--filepath' => $tmp, '--filecontents' => base64_encode('hello'),
            ])));
            self::assertSame(Command::INVALID, $exit);
            self::assertStringContainsString('mutually exclusive', $t->getDisplay());
        } finally {
            unlink($tmp);
        }
    }

    #[Test]
    public function upload_requires_one_of_filepath_or_filecontents(): void
    {
        $t = $this->tester(new UploadFileCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, [
            '--bank' => 'nordea', '--filetype' => 'KTL', '--filename' => 'x',
        ])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('One of --filepath or --filecontents', $t->getDisplay());
    }

    #[Test]
    public function upload_fails_when_filepath_does_not_exist(): void
    {
        $t = $this->tester(new UploadFileCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, [
            '--bank' => 'nordea', '--filetype' => 'KTL', '--filename' => 'x',
            '--filepath' => '/tmp/this-file-does-not-exist-' . uniqid(),
        ])));
        self::assertSame(Command::FAILURE, $exit);
        self::assertStringContainsString('Cannot read --filepath', $t->getDisplay());
    }

    #[Test]
    public function download_requires_bank_filetype_filereference(): void
    {
        $t = $this->tester(new DownloadFileCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--bank' => 'nordea'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--filereference', $t->getDisplay());
    }

    #[Test]
    public function delete_requires_bank_filetype_filereference(): void
    {
        $t = $this->tester(new DeleteFileCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--bank' => 'nordea', '--filetype' => 'KTL'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--filereference', $t->getDisplay());
    }

    #[Test]
    public function sync_requires_bank_and_syncdir(): void
    {
        $t = $this->tester(new DownloadFilesCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::AUTH, ['--bank' => 'nordea'])));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--syncdir', $t->getDisplay());
    }
}
