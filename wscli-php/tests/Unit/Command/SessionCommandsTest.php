<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Session\InitLoginCommand;
use IsecureFi\WsCli\Command\Session\LoginCommand;
use IsecureFi\WsCli\Command\Session\LoginMfaCommand;
use IsecureFi\WsCli\Command\Session\LogoutCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

final class SessionCommandsTest extends CommandTestCase
{
    #[Test]
    public function init_login_requires_email_and_mode(): void
    {
        $t = $this->tester(new InitLoginCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--email and --mode are required', $t->getDisplay());
    }

    #[Test]
    public function login_rejects_invalid_mode(): void
    {
        $t = $this->tester(new LoginCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--mode'  => 'sudo',
            '--noninteractive' => true,
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--mode (admin|data)', $t->getDisplay());
    }

    #[Test]
    public function login_fails_noninteractive_without_password(): void
    {
        $t = $this->tester(new LoginCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--mode'  => 'data',
            '--noninteractive' => true,
        ]));
        self::assertSame(Command::FAILURE, $exit);
        self::assertStringContainsString('--password missing', $t->getDisplay());
    }

    #[Test]
    public function login_mfa_requires_email_code_session(): void
    {
        $t = $this->tester(new LoginMfaCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--mode' => 'admin',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--email, --code and --session are required', $t->getDisplay());
    }

    #[Test]
    public function login_mfa_requires_admin_mode(): void
    {
        $t = $this->tester(new LoginMfaCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--mode'  => 'data',
            '--code'  => '123456',
            '--session' => 'sess',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--mode=admin', $t->getDisplay());
    }

    #[Test]
    public function logout_requires_an_idtoken_in_options_or_settings(): void
    {
        $t = $this->tester(new LogoutCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--apikey' => 'AB',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('Missing --idtoken', $t->getDisplay());
    }

    #[Test]
    public function init_login_advertises_help_for_every_documented_option(): void
    {
        $cmd = new InitLoginCommand();
        $def = $cmd->getDefinition();
        foreach (['config', 'environment', 'host', 'json', 'no-save', 'email', 'mode', 'apikey', 'idtoken'] as $name) {
            self::assertTrue($def->hasOption($name), "InitLogin should declare --$name");
        }
    }
}
