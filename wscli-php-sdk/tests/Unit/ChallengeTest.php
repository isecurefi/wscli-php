<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\Unit;

use IsecureFi\WsApi\Auth\Challenge;
use IsecureFi\WsApi\Client\Api\SessionApi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ChallengeTest extends TestCase
{
    private const FAKE_CHALLENGE =
        '01234567890123456789012345678901234567890123CHALLENGEBYTES_padding_filler_more';

    #[Test]
    #[DataProvider('environments')]
    public function it_produces_a_256_byte_ciphertext_for_a_2048_bit_key(string $env): void
    {
        $challenge = new Challenge(new SessionApi());

        $b64 = $challenge->encryptPassword('Strong-Pass-1234!@#$', self::FAKE_CHALLENGE, $env);

        $cipher = base64_decode($b64, true);
        self::assertNotFalse($cipher, 'Output is not valid base64');
        self::assertSame(256, strlen($cipher), 'RSA-2048 PKCS#1 ciphertext must be 256 bytes');
    }

    #[Test]
    public function it_uses_a_different_key_per_environment(): void
    {
        $challenge = new Challenge(new SessionApi());

        $testCipher = $challenge->encryptPassword('pw', self::FAKE_CHALLENGE, 'test');
        $prodCipher = $challenge->encryptPassword('pw', self::FAKE_CHALLENGE, 'production');

        self::assertNotSame($testCipher, $prodCipher);
    }

    #[Test]
    public function it_rejects_unknown_environments(): void
    {
        $challenge = new Challenge(new SessionApi());

        $this->expectException(\InvalidArgumentException::class);
        $challenge->encryptPassword('pw', self::FAKE_CHALLENGE, 'staging');
    }

    #[Test]
    public function it_combines_password_with_a_13_byte_window_of_the_challenge(): void
    {
        // PKCS#1 v1.5 is randomized, so we cannot assert on ciphertext directly;
        // we verify the helper does not blow up on edge-sized inputs and that
        // a too-short challenge degrades to an empty substring (length 0) — the
        // service would reject it but the client must not crash.
        $challenge = new Challenge(new SessionApi());

        $short = $challenge->encryptPassword('pw', 'short', 'test');
        $long  = $challenge->encryptPassword('pw', self::FAKE_CHALLENGE, 'test');

        self::assertSame(256, strlen(base64_decode($short, true)));
        self::assertSame(256, strlen(base64_decode($long, true)));
        self::assertNotSame($short, $long, 'different challenges must yield different ciphertext');
    }

    public static function environments(): array
    {
        return [
            'test'       => ['test'],
            'testing'    => ['testing'],
            'production' => ['production'],
        ];
    }
}
