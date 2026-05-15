<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\E2E;

use IsecureFi\WsCli\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Base class for CLI-level e2e tests that drive the full Symfony Console
 * pipeline against ws-api.test.isecure.fi.
 *
 * Tests automatically skip when WSAPI_TEST_EMAIL / WSAPI_TEST_PASSWORD are
 * missing, keeping CI green in environments without test credentials.
 *
 * For each test class we:
 *   - mint a fresh settings.yaml in a tempdir
 *   - log in once (data-mode) via `session:login` and persist idtoken+apikey
 *   - hand a configured ApplicationTester to each test
 *   - drop the settings.yaml in tearDownAfterClass
 *
 * Admin-mode tests gate on WSAPI_TEST_MFA_CODE — set it just before running
 * those tests since SMS codes expire quickly.
 */
abstract class CliE2ETestCase extends TestCase
{
    protected ?string $settingsPath = null;
    protected Application $app;
    protected ApplicationTester $tester;

    /** Track artifacts each test creates so tearDown can clean them up. */
    protected array $createdFileRefs = [];
    protected array $createdPgpKeyIds = [];
    protected array $sharedExtEmails = [];

    protected function setUp(): void
    {
        parent::setUp();
        $email = $this->env('WSAPI_TEST_EMAIL');
        $password = $this->env('WSAPI_TEST_PASSWORD');
        if (!$email || !$password) {
            $this->markTestSkipped('Set WSAPI_TEST_EMAIL and WSAPI_TEST_PASSWORD to run CLI e2e tests.');
        }

        putenv('WSCLI_RELAX_PERMS=1');
        $this->settingsPath = tempnam(sys_get_temp_dir(), 'wscli-e2e-');
        file_put_contents($this->settingsPath, "settings: {}\n");
        chmod($this->settingsPath, 0600);

        $this->app = new Application();
        $this->app->setAutoExit(false);
        $this->tester = new ApplicationTester($this->app);

        if ($this->loginMode() === 'data') {
            $this->loginAsDataUser($email, $password);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFileRefs as [$bank, $type, $ref]) {
            $this->runCli([
                'command' => 'files:delete',
                '--bank' => $bank,
                '--filetype' => $type,
                '--filereference' => $ref,
            ]);
        }
        foreach ($this->createdPgpKeyIds as $keyId) {
            $this->runCli(['command' => 'pgp:delete', '--pgpkeyid' => $keyId]);
        }
        foreach ($this->sharedExtEmails as $email) {
            $this->runCli(['command' => 'certs:unshare', '--extemail' => $email]);
        }
        if ($this->settingsPath && is_file($this->settingsPath)) {
            unlink($this->settingsPath);
        }
        parent::tearDown();
    }

    /** Override in admin-flavour tests to skip the data-mode auto-login. */
    protected function loginMode(): string
    {
        return 'data';
    }

    protected function env(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);
        return ($value === false || $value === '') ? $default : $value;
    }

    /**
     * Runs a command via ApplicationTester. The settings file is auto-applied;
     * pass `--config` in $args to override.
     *
     * @param array<string, mixed> $args
     */
    protected function runCli(array $args, array $options = []): int
    {
        $args['--config'] = $args['--config'] ?? $this->settingsPath;
        return $this->tester->run($args, $options + ['interactive' => false]);
    }

    /** Convenience: assert the previous run succeeded and return decoded JSON. */
    protected function assertCliJson(int $exitCode, ?string $msg = null): array
    {
        self::assertSame(0, $exitCode, ($msg ?? 'CLI exit code') . ': ' . $this->tester->getDisplay());
        $display = trim($this->tester->getDisplay());
        $decoded = json_decode($display, true);
        self::assertIsArray($decoded, 'CLI output was not JSON: ' . $display);
        return $decoded;
    }

    private function loginAsDataUser(string $email, string $password): void
    {
        $exit = $this->runCli([
            'command' => 'session:login',
            '--email'    => $email,
            '--password' => $password,
            '--mode'     => 'data',
            '--noninteractive' => true,
            '--json' => true,
        ]);
        if ($exit !== 0) {
            $this->fail('data-mode login failed: ' . $this->tester->getDisplay());
        }
    }

    /** Track an uploaded file for cleanup. */
    protected function trackFile(string $bank, string $fileType, string $ref): void
    {
        $this->createdFileRefs[] = [$bank, $fileType, $ref];
    }

    protected function trackPgpKey(string $keyId): void
    {
        $this->createdPgpKeyIds[] = $keyId;
    }

    protected function trackShare(string $extEmail): void
    {
        $this->sharedExtEmails[] = $extEmail;
    }
}
