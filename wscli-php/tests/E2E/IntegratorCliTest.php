<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('integrator')]
final class IntegratorCliTest extends CliE2ETestCase
{
    #[Test]
    public function list_accounts_returns_a_collection(): void
    {
        $exit = $this->runCli(['command' => 'integrator:list-accounts', '--json' => true]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
    }
}
