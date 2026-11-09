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
    #[\PHPUnit\Framework\Attributes\DataProvider('operations')]
    public function testDefaultAndExplicitLegacyWire(string $operation, bool $legacy): void
    {
        $vectors = json_decode(file_get_contents(__DIR__.'/../Fixtures/password-profile/vectors.json'), true, 16, JSON_THROW_ON_ERROR);
        $case = $vectors['Cases'][0];
        $key = $vectors['Keys'][0];
        $discovery = $legacy ? ['Challenge'=>str_repeat('x',45).'1789841000000'] : $case + ['PublicKey'=>$key['PublicKey']];
        $ok = ['ResponseCode'=>'00','ResponseText'=>'OK'];
        $responses = [new \GuzzleHttp\Psr7\Response(200, [], json_encode($discovery + $ok)), new \GuzzleHttp\Psr7\Response(200, [], json_encode($ok))];
        if ($legacy && $operation === 'reset') {
            $responses = [new \GuzzleHttp\Psr7\Response(200, [], json_encode($ok)), $responses[0], $responses[1]];
        }
        $history = [];
        $stack = \GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler($responses));
        $stack->push(\GuzzleHttp\Middleware::history($history));
        $http = new \GuzzleHttp\Client(['handler'=>$stack]);
        $config = new \IsecureFi\WsApi\Client\Configuration(); $config->setHost('https://example.invalid/v2');
        $client = $legacy ? new PasswordProfile($config, $http, PasswordProfile::LEGACY, 'test') : new PasswordProfile($config, $http);
        if ($operation === 'login') $client->login('user@example.invalid','data',$case['Password']);
        elseif ($operation === 'register') $client->register('user@example.invalid','data',$case['Password'],'tenant','Company','Name','+358000000000');
        else { $client->initPasswordReset('user@example.invalid','data'); $client->passwordReset('user@example.invalid','data','123456',$case['Password']); }
        $body = json_decode((string) $history[count($history)-1]['request']->getBody(), true, 16, JSON_THROW_ON_ERROR);
        if ($legacy) {
            self::assertArrayNotHasKey('EncryptionProfile',$body); self::assertArrayNotHasKey('KeyId',$body);
            foreach ($history as $entry) self::assertSame('', $entry['request']->getUri()->getQuery());
            self::assertSame(256, strlen(base64_decode($body['Encrypted'],true)));
        } else {
            self::assertSame(PasswordProfile::NAME,$body['EncryptionProfile']);
            self::assertStringContainsString('EncryptionProfile=', $history[0]['request']->getUri()->getQuery());
            if ($operation === 'register') self::assertStringContainsString('ApiKey=tenant',$history[0]['request']->getUri()->getQuery());
            if ($operation === 'reset') self::assertStringEndsWith('/password',$history[0]['request']->getUri()->getPath());
            $private = PublicKeyLoader::loadPrivateKey($key['PrivateKey'])->withPadding(RSA::ENCRYPTION_OAEP)->withHash('sha256')->withMGFHash('sha256')->withLabel('');
            self::assertSame($case['FrameHex'],bin2hex($private->decrypt(base64_decode($body['Encrypted'],true))));
        }
    }
    public static function operations(): array
    {
        return [['login',false],['register',false],['reset',false],['login',true],['register',true],['reset',true]];
    }
    public function testDefaultRefusesOldServerWithoutMutation(): void
    {
        $history=[];
        $stack=\GuzzleHttp\HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([new \GuzzleHttp\Psr7\Response(200,[], '{"ResponseCode":"00","Challenge":"old-server"}')]));
        $stack->push(\GuzzleHttp\Middleware::history($history));
        $config=new \IsecureFi\WsApi\Client\Configuration();$config->setHost('https://example.invalid/v2');
        try {(new PasswordProfile($config,new \GuzzleHttp\Client(['handler'=>$stack])))->login('user@example.invalid','data','Synthetic-42!'); self::fail('Expected refusal');}
        catch (\InvalidArgumentException $e) {self::assertCount(1,$history);self::assertSame('GET',$history[0]['request']->getMethod());}
    }
    public function testLegacyRequiresExplicitEnvironment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new PasswordProfile(new \IsecureFi\WsApi\Client\Configuration(), new \GuzzleHttp\Client(), PasswordProfile::LEGACY);
    }
}
