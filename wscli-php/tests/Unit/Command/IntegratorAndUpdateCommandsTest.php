<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Command\Integrator\ListAccountsCommand;
use IsecureFi\WsCli\Command\RollbackCommand;
use IsecureFi\WsCli\Command\SelfUpdateCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class IntegratorAndUpdateCommandsTest extends CommandTestCase
{
    #[Test]
    public function list_accounts_requires_auth(): void
    {
        $t = $this->tester(new ListAccountsCommand());
        $exit = $t->execute($this->withConfig());
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('Missing --idtoken', $t->getDisplay());
    }

    #[Test]
    public function self_update_refuses_outside_phar(): void
    {
        $t = new CommandTester(new SelfUpdateCommand());
        $exit = $t->execute([]);
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('only meaningful when running from a phar', $t->getDisplay());
    }

    #[Test]
    public function rollback_refuses_outside_phar(): void
    {
        $t = new CommandTester(new RollbackCommand());
        $exit = $t->execute([]);
        self::assertSame(Command::INVALID, $exit);
        self::assertStringContainsString('only meaningful when running from a phar', $t->getDisplay());
    }
}
