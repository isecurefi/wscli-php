<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('session')]
final class SessionCliTest extends CliE2ETestCase
{
    #[Test]
    public function init_login_returns_a_challenge(): void
    {
        $exit = $this->runCli([
            'command' => 'session:init-login',
            '--email' => $this->env('WSAPI_TEST_EMAIL'),
            '--mode'  => 'data',
            '--json'  => true,
        ]);
        $data = $this->assertCliJson($exit);
        self::assertNotEmpty($data['challenge'] ?? '');
    }

    #[Test]
    public function login_persists_idtoken_and_apikey_into_settings(): void
    {
        // setUp() already logged us in; verify the settings.yaml was written.
        $contents = file_get_contents($this->settingsPath);
        self::assertStringContainsString('idtoken:', $contents);
        self::assertStringContainsString('apikey:',  $contents);
    }

    #[Test]
    public function logout_clears_idtoken_and_revokes_on_server(): void
    {
        $exit = $this->runCli([
            'command' => 'session:logout',
            '--email' => $this->env('WSAPI_TEST_EMAIL'),
            '--mode'  => 'data',
            '--json'  => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
        $contents = file_get_contents($this->settingsPath);
        self::assertStringNotContainsString('idtoken:', $contents);
    }

    #[Test]
    public function bogus_password_yields_api_error(): void
    {
        $exit = $this->runCli([
            'command'  => 'session:login',
            '--config' => tempnam(sys_get_temp_dir(), 'wscli-e2e-bogus-'),
            '--email'  => $this->env('WSAPI_TEST_EMAIL'),
            '--mode'   => 'data',
            '--password' => 'this-is-not-the-password-1234!',
            '--noninteractive' => true,
            '--json'   => true,
        ]);
        self::assertNotSame(Command::SUCCESS, $exit);
        self::assertStringContainsString('API error', $this->tester->getDisplay());
    }
}
