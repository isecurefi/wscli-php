<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Pgp\DeleteKeyCommand;
use IsecureFi\WsCli\Command\Pgp\ListKeysCommand;
use IsecureFi\WsCli\Command\Pgp\UploadKeyCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

final class PgpCommandsTest extends CommandTestCase
{
    private const ADMIN_AUTH = ['--idtoken' => 'ey', '--apikey' => 'AB', '--mode' => 'admin'];

    #[Test]
    public function list_keys_requires_auth(): void
    {
        $t = $this->tester(new ListKeysCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('Missing --idtoken', $t->getDisplay());
    }

    #[Test]
    public function upload_key_requires_one_of_contents_or_file(): void
    {
        $t = $this->tester(new UploadKeyCommand());
        $exit = $t->execute($this->withConfig(self::ADMIN_AUTH));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--pgpkeycontents or --pgpkeyfile', $t->getDisplay());
    }

    #[Test]
    public function upload_key_fails_on_missing_file(): void
    {
        $t = $this->tester(new UploadKeyCommand());
        $exit = $t->execute($this->withConfig(array_merge(self::ADMIN_AUTH, [
            '--pgpkeyfile' => '/tmp/no-such-pgp-' . uniqid(),
        ])));
        self::assertSame(Command::FAILURE, $exit);
        self::assertStringContainsString('Cannot read --pgpkeyfile', $t->getDisplay());
    }

    #[Test]
    public function delete_key_requires_pgpkeyid(): void
    {
        $t = $this->tester(new DeleteKeyCommand());
        $exit = $t->execute($this->withConfig(self::ADMIN_AUTH));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--pgpkeyid', $t->getDisplay());
    }

    #[Test]
    public function upload_requires_admin_mode(): void
    {
        $t = $this->tester(new UploadKeyCommand());
        $exit = $t->execute($this->withConfig([
            '--idtoken' => 'ey', '--apikey' => 'AB', '--mode' => 'data',
            '--pgpkeycontents' => '-----BEGIN-----',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--mode=admin', $t->getDisplay());
    }

    #[Test]
    public function delete_requires_admin_mode(): void
    {
        $t = $this->tester(new DeleteKeyCommand());
        $exit = $t->execute($this->withConfig([
            '--idtoken' => 'ey', '--apikey' => 'AB', '--mode' => 'data',
            '--pgpkeyid' => 'whatever',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--mode=admin', $t->getDisplay());
    }
}
