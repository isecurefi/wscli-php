<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('account')]
final class AccountCliTest extends CliE2ETestCase
{
    #[Test]
    public function init_register_returns_a_challenge_for_a_new_email(): void
    {
        $exit = $this->runCli([
            'command' => 'account:init-register',
            '--email' => 'cli-probe-' . uniqid() . '@example.invalid',
            '--mode'  => 'data',
            '--json'  => true,
        ]);
        $data = $this->assertCliJson($exit);
        self::assertNotEmpty($data['challenge'] ?? '');
    }

    #[Test]
    public function init_password_reset_for_known_email(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_PASSWORD_RESET_INIT') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_PASSWORD_RESET_INIT=1 to dispatch a real reset SMS.');
        }
        $exit = $this->runCli([
            'command' => 'account:init-password-reset',
            '--email' => $this->env('WSAPI_TEST_EMAIL'),
            '--mode'  => 'data',
            '--json'  => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
    }
}
