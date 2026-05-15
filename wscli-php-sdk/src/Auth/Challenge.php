<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Auth;

use IsecureFi\WsApi\Client\Api\SessionApi;
use IsecureFi\WsApi\Client\Api\AccountApi;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey as RsaPublicKey;

/**
 * Handles the challenge-response handshake used by the ISECure WS-Channel API:
 *
 *   1. Client calls InitLogin (or InitRegister) and receives a challenge string.
 *   2. Client RSA-encrypts `password || "||" || substr(challenge, 45, 13)`
 *      using a hardcoded service public key (per environment) with PKCS#1 v1.5 padding.
 *   3. The base64-encoded ciphertext is sent as `Encrypted` in the Login / Register body.
 *
 * The two public keys below are the production / test RSA keys carried verbatim
 * from the original WsCli.php; they are intentionally hardcoded by the service.
 */
final class Challenge
{
    public const ENV_TEST = 'test';
    public const ENV_PRODUCTION = 'production';

    private const PUBLIC_KEY_TEST =
        "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQ" .
        "EAkuSaoSZztGAIGDTY7RffpsBHJJT1k207UodOJbYF" .
        "hHAq0lWJnvMPLl5Q1DUUZdTGtTdL8Dsaj/Bo2+gSyk" .
        "MMR5QiKewvQsLfvqjwOO8JDItnhJl0lUqcPpdQV4M/" .
        "Ai3YNRjNcVy4a+pichqtSAWl9S1HV01MNeouk8PEr/" .
        "zoUasmgfO3mz6N6XTUtF/tIi8K2kBOsLAtqltihFSd" .
        "/zT8ifYZE9cZTJ09lUs7kMz1wxFIsiegaE1jUYV+VS" .
        "Lu3PJ97oKhQpqop8EnkBAoBl6rmdmFryBQIdakPIdd" .
        "4rO5Yg+to10n4u7Wij9ePIwWMfbqY4QoW5nXqMgFJQ" .
        "kIt4TGeQIDAQAB";

    private const PUBLIC_KEY_PRODUCTION =
        "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQ" .
        "EA7wx4l7P3eLsaEyK7ZRMEg5urEHwaEoY9LjkYcpMw" .
        "9gmPIi3RoGjQX7HzPad2D7ES2yIGdmyxjN8R2LyFa8" .
        "keEE+VY3ISYzP2cOjd/zDkX01yjDXQLRxntXbtqIyp" .
        "GQAzmZbCyIB226ZKEE+ldh6MYyM41YWYikfocYssFE" .
        "jY7fpPGeUg4FOmHmyWIZeMkXYovskoi1jZ1Ay1qn95" .
        "XlpA/Ptru2efro4T1xksv4WBBrj8bMNwdDpf4oyzH2" .
        "PKYkn3/KlNTBCHlAmzP0jd4pIaN0tAf2m8TcNq7kuB" .
        "zyfs8AcCUj870p8SEiko0PMx6K+zVsTVWsxfUX+/+k" .
        "mapmp/AwIDAQAB";

    public function __construct(
        private readonly SessionApi $sessionApi,
        private readonly ?AccountApi $accountApi = null,
    ) {
    }

    /**
     * Fetches a fresh challenge string for the given (email, mode) pair.
     *
     * The same endpoint is reused for both login and registration flows on the
     * service side; pass $useRegister=true to call InitRegister via AccountApi.
     */
    public function fetch(string $email, string $mode, bool $useRegister = false): string
    {
        if ($useRegister) {
            if ($this->accountApi === null) {
                throw new \LogicException('AccountApi must be provided to fetch a register challenge.');
            }
            $response = $this->accountApi->initRegister($email, $mode);
        } else {
            $response = $this->sessionApi->initLogin($email, $mode);
        }

        $challenge = $response->getChallenge();
        if (!is_string($challenge) || $challenge === '') {
            throw new \RuntimeException('API did not return a challenge string.');
        }
        return $challenge;
    }

    /**
     * RSA-encrypts the password bound to the per-session challenge, returning
     * the base64 ciphertext that the API expects in the `Encrypted` body field.
     *
     * Mirrors the original WsCli.php helper: encrypts `password||substr(challenge,45,13)`
     * with PKCS#1 v1.5 padding under the environment-specific service public key.
     */
    public function encryptPassword(string $password, string $challenge, string $environment): string
    {
        $publicKeyBase64 = match ($environment) {
            self::ENV_TEST, 'testing' => self::PUBLIC_KEY_TEST,
            self::ENV_PRODUCTION => self::PUBLIC_KEY_PRODUCTION,
            default => throw new \InvalidArgumentException("Unsupported environment: $environment"),
        };

        $der = base64_decode($publicKeyBase64, true);
        if ($der === false) {
            throw new \RuntimeException('Service public key is not valid base64.');
        }
        $pem = "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($der), 64, "\n")
            . "-----END PUBLIC KEY-----\n";

        // Force phpseclib's pure-PHP OAEP engine: PHP's openssl_public_encrypt()
        // ignores the 5th-argument hash phpseclib3 tries to pass, so the
        // OpenSSL path silently produces an OAEP-SHA1 ciphertext that the
        // ws-api.test.isecure.fi backend rejects with "oaep decoding error".
        // The pure-PHP path produces ciphertext that the service accepts.
        RSA::forceEngine('PHP');

        $loaded = PublicKeyLoader::loadPublicKey($pem);
        if (!$loaded instanceof RsaPublicKey) {
            throw new \RuntimeException('Service public key did not parse as an RSA public key.');
        }
        $key = $loaded
            ->withPadding(RSA::ENCRYPTION_OAEP)
            ->withHash('sha1')
            ->withMGFHash('sha1');

        $plain = $password . '||' . substr($challenge, 45, 13);
        $cipher = $key->encrypt($plain);

        return base64_encode($cipher);
    }
}
