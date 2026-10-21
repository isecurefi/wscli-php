<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use IsecureFi\WsApi\Client\Api\SessionApi;
use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsApi\Client\Model\LoginMFAReq;
use IsecureFi\WsApi\Client\Model\LoginReq;
use Psr\Http\Message\RequestInterface;

/** Preserves MFA fields that the legacy generated response models omit. */
final class SessionLogin
{
    private ClientInterface $client;
    private SessionApi $api;

    public function __construct(private Configuration $config, ?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client();
        $this->api = new SessionApi($this->client, $config);
    }

    /** @return array<string, mixed> */
    public function login(string $email, string $mode, LoginReq $body): array
    {
        return $this->send($this->api->loginRequest($email, $mode, $body));
    }

    public static function factor(string $name): string
    {
        if (!in_array($name, ['SMS_MFA', 'SOFTWARE_TOKEN_MFA'], true)) {
            throw new \InvalidArgumentException('The server returned an unsupported MFA challenge.');
        }
        return $name;
    }

    /** @return array<string, mixed> */
    public function selectFactor(string $email, string $session, string $factor): array
    {
        $factor = self::factor($factor);
        $url = rtrim($this->config->getHost(), '/') . '/session/' . rawurlencode($email) . '/admin/selectmfa';
        $response = $this->send(new Request('PUT', $url, [
            'Content-Type' => 'application/json', 'Accept' => 'application/json',
            'User-Agent' => $this->config->getUserAgent(),
        ], json_encode(['MfaType' => $factor, 'Session' => $session], JSON_THROW_ON_ERROR)));
        if (($response['ChallengeName'] ?? null) !== $factor || !is_string($response['Session'] ?? null) || $response['Session'] === '') {
            throw new \RuntimeException('Invalid MFA selection response; settings were not changed.');
        }
        return $response;
    }

    /** @return array<string, mixed> */
    public function mfa(string $email, string $code, string $session, string $factor): array
    {
        $factor = self::factor($factor);
        $request = $this->api->loginMFARequest($email, 'admin', new LoginMFAReq(['code' => $code, 'session' => $session]));
        $request = $request->withBody(Utils::streamFor(json_encode([
            'Code' => $code, 'Session' => $session, 'ChallengeName' => $factor,
        ], JSON_THROW_ON_ERROR)));
        $response = $this->send($request);
        self::requireSession($response);
        return $response;
    }

    /** @param array<string, mixed> $response */
    public static function requireSession(array $response): void
    {
        if (empty($response['IdToken']) || empty($response['ApiKey'])) {
            throw new \RuntimeException('Login did not return an authenticated session; settings were not changed.');
        }
    }

    /** @return array<string, mixed> */
    private function send(RequestInterface $request): array
    {
        try {
            $response = $this->client->send($request, ['http_errors' => false, 'allow_redirects' => false, 'timeout' => 30]);
        } catch (GuzzleException $e) {
            throw new ApiException('Authentication request failed. Check the connection and try again.');
        }
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new ApiException('Authentication request rejected. Check the credentials and try again.', $response->getStatusCode());
        }
        try {
            $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Invalid authentication response; settings were not changed.');
        }
        if (!is_array($data) || ($data['ResponseCode'] ?? null) !== '00') {
            throw new \RuntimeException('Authentication failed; settings were not changed. Check your password or use a fresh verification code.');
        }
        return $data;
    }
}
