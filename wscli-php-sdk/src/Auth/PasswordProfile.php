<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsApi\Client\Api\SessionApi;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey;

/** Standard password authentication helper. Stronger profile is the default; legacy is explicit. */
final class PasswordProfile
{
    public const NAME = 'password-rsa-oaep-sha256-v1';
    public const LEGACY = 'legacy';
    private Configuration $config;
    /** @var array<string,string>|null */
    private ?array $reset = null;
    private ?string $resetIdentity = null;

    public function __construct(Configuration $config, private readonly ClientInterface $client,
        private readonly string $profile = self::NAME, private readonly ?string $legacyEnvironment = null)
    {
        if (!in_array($profile, [self::NAME, self::LEGACY], true) ||
            ($profile === self::LEGACY && !in_array($legacyEnvironment, [Challenge::ENV_TEST, 'testing', Challenge::ENV_PRODUCTION], true))) {
            throw new \InvalidArgumentException('Select a supported profile and an explicit environment for legacy.');
        }
        $this->config = clone $config;
        $uri = parse_url($config->getHost());
        if (($uri['scheme'] ?? '') !== 'https' || isset($uri['user']) || isset($uri['pass']) || isset($uri['query']) || isset($uri['fragment'])) {
            throw new \InvalidArgumentException('Password discovery requires an HTTPS API endpoint.');
        }
    }

    /** @param array<string,mixed> $value @return array<string,string> */
    public static function validate(array $value): array
    {
        foreach (['EncryptionProfile', 'KeyId', 'Challenge', 'PublicKey'] as $field) {
            if (!isset($value[$field]) || !is_string($value[$field])) {
                throw new \InvalidArgumentException('Invalid password profile discovery.');
            }
        }
        if ($value['EncryptionProfile'] !== self::NAME ||
            !preg_match('/\A[A-Za-z0-9_-]{1,64}\z/', $value['KeyId']) ||
            strlen($value['Challenge']) > 3072 ||
            !preg_match('/\Ap1\.[A-Za-z0-9_-]{1,64}\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]{43}\z/', $value['Challenge']) ||
            strlen($value['PublicKey']) > 2048 || !str_starts_with($value['PublicKey'], '-----BEGIN PUBLIC KEY-----')) {
            throw new \InvalidArgumentException('Requested password profile unavailable; no legacy fallback.');
        }
        return array_intersect_key($value, array_flip(['EncryptionProfile', 'KeyId', 'Challenge', 'PublicKey']));
    }

    /** @param array<string,mixed> $discovery @return array<string,string> */
    public static function encrypt(array $discovery, string $password): array
    {
        $discovery = self::validate($discovery);
        if (!mb_check_encoding($password, 'UTF-8') || strlen($password) < 1 || strlen($password) > 283) {
            throw new \InvalidArgumentException('Password must contain 1–283 valid UTF-8 bytes.');
        }
        RSA::forceEngine('PHP');
        $key = PublicKeyLoader::loadPublicKey($discovery['PublicKey']);
        if (!$key instanceof PublicKey || !in_array($key->getLength(), [3072,4096], true)) {
            throw new \InvalidArgumentException('Unsupported password encryption key.');
        }
        $parts = \phpseclib3\Crypt\RSA\Formats\Keys\PKCS8::load($key->toString('PKCS8'));
        if ((string) $parts['publicExponent'] !== '65537') {
            throw new \InvalidArgumentException('Unsupported password encryption exponent.');
        }
        $key = $key->withPadding(RSA::ENCRYPTION_OAEP)->withHash('sha256')->withMGFHash('sha256')->withLabel('');
        $plain = "\x01" . hash('sha256', $discovery['Challenge'], true) . pack('n', strlen($password)) . $password;
        $ciphertext = $key->encrypt($plain);
        return ['ChResp' => $discovery['Challenge'], 'EncryptionProfile' => self::NAME,
            'KeyId' => $discovery['KeyId'], 'Encrypted' => base64_encode($ciphertext)];
    }

    /** @return array<string,mixed> */
    public function login(string $email, string $mode, string $password): array
    {
        $path = $this->path('session', $email, $mode);
        $discovery = $this->discovery($path);
        return $this->send('POST', $path, $this->envelope($discovery, $password));
    }

    /** @return array<string,mixed> */
    public function register(string $email, string $mode, string $password, string $tenant,
        string $company, string $name, string $phone): array
    {
        $path = $this->path('account', $email, $mode);
        $discovery = $this->discovery($path, $tenant);
        return $this->send('PUT', $path, $this->envelope($discovery, $password) +
            ['ApiKey'=>$tenant, 'Company'=>$company, 'Name'=>$name, 'Phone'=>$phone]);
    }

    public function initPasswordReset(string $email, string $mode): void
    {
        $this->reset = null; $this->resetIdentity = null;
        $path = $this->path('account', $email, $mode) . '/password';
        $value = $this->discovery($path);
        $this->reset = $this->profile === self::NAME ? self::validate($value) : [];
        $this->resetIdentity = $path;
    }

    /** @return array<string,mixed> */
    public function passwordReset(string $email, string $mode, string $code, string $password): array
    {
        $path = $this->path('account', $email, $mode) . '/password';
        $discovery = $this->reset; $identity = $this->resetIdentity;
        $this->reset = null; $this->resetIdentity = null;
        if ($discovery === null || $identity !== $path || !preg_match('/\A[0-9]{6}\z/', $code)) {
            throw new \InvalidArgumentException('Initialize password reset for this identity first.');
        }
        if ($this->profile === self::LEGACY) {
            $discovery = $this->discovery($this->path('session', $email, $mode));
        }
        return $this->send('POST', $path, $this->envelope($discovery, $password) + ['Code'=>$code]);
    }

    /** @return array<string,mixed> */
    private function discovery(string $path, ?string $tenant = null): array
    {
        if ($this->profile === self::NAME) {
            $path .= '?EncryptionProfile=' . self::NAME;
            if ($tenant !== null) $path .= '&ApiKey=' . rawurlencode($tenant);
        }
        return $this->send('GET', $path);
    }

    /** @param array<string,mixed> $discovery @return array<string,string> */
    private function envelope(array $discovery, string $password): array
    {
        if ($this->profile === self::NAME) return self::encrypt($discovery, $password);
        $challenge = $discovery['Challenge'] ?? null;
        if (!is_string($challenge) || $challenge === '') {
            throw new \InvalidArgumentException('Invalid legacy challenge.');
        }
        $legacy = new Challenge(new SessionApi($this->client, $this->config));
        return ['ChResp' => $challenge, 'Encrypted' => $legacy->encryptPassword($password, $challenge, $this->legacyEnvironment)];
    }

    private function path(string $resource, string $email, string $mode): string
    {
        if (!in_array($mode, ['admin','data'], true)) throw new \InvalidArgumentException('Invalid account mode.');
        return '/' . $resource . '/' . rawurlencode($email) . '/' . $mode;
    }

    /** @param array<string,string>|null $body @return array<string,mixed> */
    private function send(string $method, string $path, ?array $body = null): array
    {
        $request = new Request($method, rtrim($this->config->getHost(), '/') . $path,
            ['Content-Type'=>'application/json', 'Accept'=>'application/json'],
            $body === null ? null : json_encode($body, JSON_THROW_ON_ERROR));
        try {
            $response = $this->client->send($request, ['http_errors'=>false, 'allow_redirects'=>false, 'timeout'=>30]);
            $bytes = $response->getBody()->read(65537);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300 || strlen($bytes) > 65536) {
                throw new \RuntimeException('Password operation rejected.');
            }
            $value = json_decode($bytes, true, 16, JSON_THROW_ON_ERROR);
            if (!is_array($value) || ($value['ResponseCode'] ?? '') !== '00') throw new \RuntimeException('Password operation rejected.');
            return $value;
        } catch (\Throwable $error) {
            // Never attach a transport exception that could contain the request body.
            throw new \RuntimeException('Password operation failed; do not automatically retry.');
        }
    }
}
