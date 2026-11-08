<?php
declare(strict_types=1);
namespace IsecureFi\WsApi\Tests\Unit;
use IsecureFi\WsApi\Auth\PasswordProfile;
use PHPUnit\Framework\TestCase;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
final class PasswordProfileTest extends TestCase
{
    public function testSharedVectors(): void
    {
        $vectors = json_decode(file_get_contents(__DIR__.'/../Fixtures/password-profile/vectors.json'), true, 16, JSON_THROW_ON_ERROR);
        foreach ($vectors['Cases'] as $case) {
            $key = array_values(array_filter($vectors['Keys'], fn($k) => $k['KeyId'] === $case['KeyId']))[0];
            $encrypted = PasswordProfile::encrypt($case + ['PublicKey'=>$key['PublicKey']], $case['Password']);
            $private = PublicKeyLoader::loadPrivateKey($key['PrivateKey'])->withPadding(RSA::ENCRYPTION_OAEP)->withHash('sha256')->withMGFHash('sha256')->withLabel('');
            self::assertSame($case['FrameHex'], bin2hex($private->decrypt(base64_decode($encrypted['Encrypted'], true))));
        }
    }
    public function testRefusesOldServerDiscovery(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PasswordProfile::encrypt(['Challenge'=>'legacy'], 'Synthetic-42!');
    }
}
