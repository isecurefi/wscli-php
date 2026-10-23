<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit;

use Composer\Semver\Semver;
use IsecureFi\WsCli\RuntimeRequirement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RuntimeRequirementTest extends TestCase
{
    public static function versions(): iterable
    {
        foreach (['8.1.34' => false, '8.2.35' => false, '8.3.35' => false, '8.3.36' => true,
            '8.3.37' => true, '8.4.0' => false, '8.4.26' => false, '8.4.27' => true,
            '8.4.28' => true, '8.5.0' => false, '8.5.11' => false, '8.5.12' => true,
            '8.5.13' => true, '8.6.0' => false, '9.0.0' => false] as $version => $supported) {
            yield $version => [$version, $supported];
        }
    }

    #[DataProvider('versions')]
    public function test_runtime_guard_and_all_package_constraints_agree(string $version, bool $supported): void
    {
        [$major, $minor, $patch] = array_map('intval', explode('.', $version));
        self::assertSame($supported, RuntimeRequirement::isSupported($major * 10000 + $minor * 100 + $patch));
        $root = dirname(__DIR__, 3);
        foreach (['wscli-php', 'wscli-php-sdk', 'wscli-php-sdk/openapi-php-sdk', 'tools/box'] as $package) {
            $manifest = json_decode(file_get_contents($root . '/' . $package . '/composer.json'), true);
            self::assertSame($supported, Semver::satisfies($version, $manifest['require']['php']), $package);
        }
    }
}
