<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Account\InitPasswordResetCommand;
use IsecureFi\WsCli\Command\Account\InitRegisterCommand;
use IsecureFi\WsCli\Command\Account\PasswordResetCommand;
use IsecureFi\WsCli\Command\Account\RegisterCommand;
use IsecureFi\WsCli\Command\Account\VerifyEmailCommand;
use IsecureFi\WsCli\Command\Account\VerifyPhoneCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

final class AccountCommandsTest extends CommandTestCase
{
    #[Test]
    public function init_register_requires_email_and_mode(): void
    {
        $t = $this->tester(new InitRegisterCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--email and --mode are required', $t->getDisplay());
    }

    #[Test]
    public function register_requires_email_mode_password_phone(): void
    {
        $t = $this->tester(new RegisterCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('is required', $t->getDisplay());
    }

    #[Test]
    public function register_enforces_minimum_password_length(): void
    {
        $t = $this->tester(new RegisterCommand());
        $exit = $t->execute($this->withConfig([
            '--email'    => 'a@b.c',
            '--mode'     => 'data',
            '--password' => 'short',
            '--phone'    => '+358401234567',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('at least 20 characters', $t->getDisplay());
    }

    #[Test]
    public function verify_email_requires_four_fields(): void
    {
        $t = $this->tester(new VerifyEmailCommand());
        $exit = $t->execute($this->withConfig(['--email' => 'a@b.c', '--mode' => 'data']));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--code', $t->getDisplay());
    }

    #[Test]
    public function verify_phone_requires_four_fields(): void
    {
        $t = $this->tester(new VerifyPhoneCommand());
        $exit = $t->execute($this->withConfig(['--email' => 'a@b.c', '--mode' => 'data', '--code' => '123456']));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('--phone', $t->getDisplay());
    }

    #[Test]
    public function init_password_reset_requires_email_mode(): void
    {
        $t = $this->tester(new InitPasswordResetCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
    }

    #[Test]
    public function password_reset_enforces_password_length(): void
    {
        $t = $this->tester(new PasswordResetCommand());
        $exit = $t->execute($this->withConfig([
            '--email' => 'a@b.c',
            '--mode' => 'data',
            '--password' => 'short',
            '--code' => '123456',
        ]));
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('at least 20 characters', $t->getDisplay());
    }

    #[Test]
    public function every_account_command_supports_json_and_no_save_flags(): void
    {
        $commands = [
            new InitRegisterCommand(),
            new RegisterCommand(),
            new VerifyEmailCommand(),
            new VerifyPhoneCommand(),
            new InitPasswordResetCommand(),
            new PasswordResetCommand(),
        ];
        foreach ($commands as $cmd) {
            $def = $cmd->getDefinition();
            self::assertTrue($def->hasOption('json'),     $cmd->getName() . ' must support --json');
            self::assertTrue($def->hasOption('no-save'),  $cmd->getName() . ' must support --no-save');
            self::assertTrue($def->hasOption('config'),   $cmd->getName() . ' must support --config');
            self::assertTrue($def->hasOption('environment'), $cmd->getName() . ' must support --environment');
        }
    }
}
