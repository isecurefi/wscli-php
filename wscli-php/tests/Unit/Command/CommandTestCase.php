<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Shared test base for CommandTester-driven unit tests. Each test gets an
 * isolated empty settings.yaml so that no real ~/.wscli/settings.yaml
 * accidentally leaks state into the assertions.
 */
abstract class CommandTestCase extends TestCase
{
    protected string $configPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configPath = tempnam(sys_get_temp_dir(), 'wscli-test-cfg-');
        file_put_contents($this->configPath, "settings: {}\n");
        chmod($this->configPath, 0600);
    }

    protected function tearDown(): void
    {
        @unlink($this->configPath);
        parent::tearDown();
    }

    protected function tester(Command $command): CommandTester
    {
        // CommandTester does not invoke the Application::run() pipeline, so
        // calling Command::run() goes through our base-class override which
        // handles InvalidArgumentException as Command::INVALID.
        return new CommandTester($command);
    }

    /** @return array<string,mixed> */
    protected function withConfig(array $extra = []): array
    {
        return array_replace(['--config' => $this->configPath, '--host' => 'https://127.0.0.1:1'], $extra);
    }
}
