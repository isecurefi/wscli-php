<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;

#[Group('e2e')]
#[Group('cli')]
#[Group('certs')]
final class CertsCliTest extends CliE2ETestCase
{
    #[Test]
    public function list_certs_works_in_data_mode(): void
    {
        $exit = $this->runCli(['command' => 'certs:list', '--json' => true]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
    }

    #[Test]
    public function config_certs_toggles_export_in_admin_mode(): void
    {
        if ($this->env('WSAPI_TEST_ADMIN_IDTOKEN') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_ADMIN_IDTOKEN to run admin-mode certs tests.');
        }
        if ($this->env('WSAPI_TEST_ALLOW_CERT_CONFIG') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_CERT_CONFIG=1 to toggle Export flag.');
        }
        $exit = $this->runCli([
            'command'  => 'certs:config',
            '--mode'   => 'admin',
            '--idtoken' => $this->env('WSAPI_TEST_ADMIN_IDTOKEN'),
            '--apikey' => $this->env('WSAPI_TEST_APIKEY'),
            '--export' => 'false',
            '--json'   => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, $this->tester->getDisplay());
    }

    #[Test]
    public function share_then_unshare_round_trip(): void
    {
        $extEmail = $this->env('WSAPI_TEST_EXT_EMAIL');
        if ($extEmail === null || $this->env('WSAPI_TEST_ADMIN_IDTOKEN') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_ADMIN_IDTOKEN and WSAPI_TEST_EXT_EMAIL to run cert sharing tests.');
        }
        $exit = $this->runCli([
            'command'    => 'certs:share',
            '--mode'     => 'admin',
            '--idtoken'  => $this->env('WSAPI_TEST_ADMIN_IDTOKEN'),
            '--apikey'   => $this->env('WSAPI_TEST_APIKEY'),
            '--extemail' => $extEmail,
            '--json'     => true,
        ]);
        self::assertSame(Command::SUCCESS, $exit, 'share: ' . $this->tester->getDisplay());
        $this->trackShare($extEmail);
    }
}
