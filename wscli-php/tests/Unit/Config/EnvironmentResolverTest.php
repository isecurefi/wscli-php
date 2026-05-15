<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Config;

use IsecureFi\WsCli\Config\EnvironmentResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EnvironmentResolverTest extends TestCase
{
    #[Test]
    #[DataProvider('envToHost')]
    public function it_picks_the_right_default_host(?string $env, string $expectedHost): void
    {
        $cfg = (new EnvironmentResolver())->build($env);
        self::assertSame($expectedHost, $cfg->getHost());
    }

    public static function envToHost(): array
    {
        return [
            'null defaults to test' => [null,         EnvironmentResolver::DEFAULT_TEST_HOST],
            'test'                  => ['test',       EnvironmentResolver::DEFAULT_TEST_HOST],
            'testing alias'         => ['testing',    EnvironmentResolver::DEFAULT_TEST_HOST],
            'production'            => ['production', EnvironmentResolver::DEFAULT_PRODUCTION_HOST],
            'uppercase production'  => ['PRODUCTION', EnvironmentResolver::DEFAULT_PRODUCTION_HOST],
        ];
    }

    #[Test]
    public function it_honors_host_overrides(): void
    {
        $cfg = (new EnvironmentResolver())->build('test', 'https://custom.example/v2');
        self::assertSame('https://custom.example/v2', $cfg->getHost());
    }

    #[Test]
    public function it_rejects_unknown_environments(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new EnvironmentResolver())->build('staging');
    }

    #[Test]
    public function normalize_for_challenge_collapses_aliases(): void
    {
        $r = new EnvironmentResolver();
        self::assertSame('test', $r->normalizeForChallenge(null));
        self::assertSame('test', $r->normalizeForChallenge('test'));
        self::assertSame('test', $r->normalizeForChallenge('testing'));
        self::assertSame('production', $r->normalizeForChallenge('production'));
    }

    #[Test]
    public function user_agent_includes_app_version(): void
    {
        $cfg = (new EnvironmentResolver())->build('test');
        self::assertStringContainsString('wscli/', $cfg->getUserAgent());
    }
}
