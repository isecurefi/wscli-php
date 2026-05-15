<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Config;

use IsecureFi\WsApi\Client\Configuration;

/**
 * Builds an SDK Configuration from the (env, host-override) pair while keeping
 * the existing convention: the production host is the test host with `.test`
 * stripped out.
 */
final class EnvironmentResolver
{
    public const TEST       = 'test';
    public const TESTING    = 'testing';
    public const PRODUCTION = 'production';

    public const DEFAULT_TEST_HOST       = 'https://ws-api.test.isecure.fi/v2';
    public const DEFAULT_PRODUCTION_HOST = 'https://ws-api.isecure.fi/v2';

    public function build(?string $environment, ?string $hostOverride = null): Configuration
    {
        $env = strtolower($environment ?? self::TEST);
        $host = $hostOverride ?? match ($env) {
            self::PRODUCTION => self::DEFAULT_PRODUCTION_HOST,
            self::TEST, self::TESTING, '' => self::DEFAULT_TEST_HOST,
            default => throw new \InvalidArgumentException("Unknown environment: $environment"),
        };

        return (new Configuration())
            ->setHost($host)
            ->setUserAgent('wscli/' . \IsecureFi\WsCli\Application::VERSION);
    }

    /** Normalises 'testing' to 'test' for Challenge::encryptPassword. */
    public function normalizeForChallenge(?string $environment): string
    {
        return match (strtolower($environment ?? self::TEST)) {
            self::PRODUCTION => self::PRODUCTION,
            default          => self::TEST,
        };
    }
}
