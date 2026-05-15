<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use IsecureFi\WsCli\Application;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Guards the full command registry so accidental refactors that drop a
 * command from Application::__construct are caught.
 */
final class ApplicationWiringTest extends TestCase
{
    private const EXPECTED = [
        'session:init-login', 'session:login', 'session:login-mfa', 'session:logout',
        'account:init-register', 'account:register', 'account:verify-email',
        'account:verify-phone', 'account:init-password-reset', 'account:password-reset',
        'certs:list', 'certs:config', 'certs:enroll', 'certs:import', 'certs:export',
        'certs:share', 'certs:unshare',
        'files:list', 'files:upload', 'files:download', 'files:delete', 'files:sync',
        'pgp:list', 'pgp:upload', 'pgp:delete',
        'integrator:list-accounts',
        'self-update', 'rollback',
    ];

    #[Test]
    public function every_documented_command_is_registered(): void
    {
        $app = new Application();
        foreach (self::EXPECTED as $name) {
            self::assertTrue($app->has($name), "Application must register `$name`");
        }
    }

    #[Test]
    public function application_metadata_is_set(): void
    {
        $app = new Application();
        self::assertSame('wscli', $app->getName());
        self::assertNotEmpty($app->getVersion());
    }
}
