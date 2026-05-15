<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Auth\Challenge;
use IsecureFi\WsApi\Client\Api\AccountApi;
use IsecureFi\WsApi\Client\Api\CertsApi;
use IsecureFi\WsApi\Client\Api\FilesApi;
use IsecureFi\WsApi\Client\Api\IntegratorApi;
use IsecureFi\WsApi\Client\Api\PgpApi;
use IsecureFi\WsApi\Client\Api\SessionApi;
use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Configuration;
use IsecureFi\WsApi\Client\Model\LoginMFAReq;
use IsecureFi\WsApi\Client\Model\LoginReq;
use PHPUnit\Framework\TestCase;

/**
 * Base class for e2e tests that exercise the live ws-api.test.isecure.fi service.
 *
 * Tests automatically skip when the required env vars are absent so the suite
 * stays green on CI runs that don't have access to test-account secrets.
 *
 * Required for any real run:
 *   WSAPI_TEST_EMAIL     - the test account email
 *   WSAPI_TEST_PASSWORD  - the test account password (>= 20 chars)
 *
 * Optional (gates additional tests):
 *   WSAPI_TEST_APIKEY        - integrator API key for authenticated calls
 *   WSAPI_TEST_PHONE         - phone number for VerifyPhone tests
 *   WSAPI_TEST_MFA_CODE      - SMS code for admin-mode login
 *   WSAPI_TEST_BANK          - bank slug for file/cert tests (default: nordea)
 *   WSAPI_TEST_EXT_EMAIL     - external email for cert-share tests
 *   WSAPI_BASE_URL           - override API host (default test endpoint)
 *   WSAPI_ENVIRONMENT        - "test" or "production" (default: test)
 */
abstract class E2ETestCase extends TestCase
{
    protected Configuration $config;
    protected Challenge $challengeHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = (new Configuration())
            ->setHost($this->env('WSAPI_BASE_URL', 'https://ws-api.test.isecure.fi/v2'))
            ->setUserAgent('wscli-php-sdk-e2e/2.0');

        $this->challengeHelper = new Challenge(
            new SessionApi(null, $this->config),
            new AccountApi(null, $this->config),
        );
    }

    protected function env(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);
        if ($value === false || $value === '') {
            return $default;
        }
        return $value;
    }

    protected function requireEnv(string $name): string
    {
        $value = $this->env($name);
        if ($value === null) {
            $this->markTestSkipped("Set $name to run this e2e test.");
        }
        return $value;
    }

    protected function sessionApi(): SessionApi
    {
        return new SessionApi(null, $this->config);
    }

    protected function accountApi(): AccountApi
    {
        return new AccountApi(null, $this->config);
    }

    protected function filesApi(): FilesApi
    {
        return new FilesApi(null, $this->config);
    }

    protected function certsApi(): CertsApi
    {
        return new CertsApi(null, $this->config);
    }

    protected function pgpApi(): PgpApi
    {
        return new PgpApi(null, $this->config);
    }

    protected function integratorApi(): IntegratorApi
    {
        return new IntegratorApi(null, $this->config);
    }

    /**
     * Performs a full data-mode login and returns [idToken, apiKey].
     * Skips the calling test if credentials aren't set.
     */
    protected function loginAsDataUser(): array
    {
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        $password = $this->requireEnv('WSAPI_TEST_PASSWORD');
        $environment = $this->env('WSAPI_ENVIRONMENT', 'test');

        $challenge = $this->challengeHelper->fetch($email, 'data');
        $encrypted = $this->challengeHelper->encryptPassword($password, $challenge, $environment);

        $req = new LoginReq(['chResp' => $challenge, 'encrypted' => $encrypted]);
        try {
            $resp = $this->sessionApi()->login($email, 'data', $req);
        } catch (ApiException $e) {
            $this->fail("data-mode login failed: " . $e->getMessage() . " body=" . $e->getResponseBody());
        }

        $idToken = $resp->getIdToken();
        $apiKey = $resp->getApiKey() ?? $this->env('WSAPI_TEST_APIKEY');
        if (!$idToken || !$apiKey) {
            $this->fail("login did not return idToken+apiKey; got: " . json_encode($resp));
        }
        return [$idToken, $apiKey];
    }

    /**
     * Performs an admin-mode login (with MFA). Requires WSAPI_TEST_MFA_CODE.
     * Returns [idToken, apiKey].
     */
    protected function loginAsAdminUser(): array
    {
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        $password = $this->requireEnv('WSAPI_TEST_PASSWORD');
        $mfaCode = $this->requireEnv('WSAPI_TEST_MFA_CODE');
        $environment = $this->env('WSAPI_ENVIRONMENT', 'test');

        $challenge = $this->challengeHelper->fetch($email, 'admin');
        $encrypted = $this->challengeHelper->encryptPassword($password, $challenge, $environment);

        $loginResp = $this->sessionApi()->login(
            $email,
            'admin',
            new LoginReq(['chResp' => $challenge, 'encrypted' => $encrypted]),
        );

        // First leg returns a Session for the MFA exchange.
        $session = $loginResp->getSession();
        if (!$session) {
            $this->fail("admin login did not return MFA session; got: " . json_encode($loginResp));
        }
        $mfaResp = $this->sessionApi()->loginMFA(
            $email,
            'admin',
            new LoginMFAReq(['code' => $mfaCode, 'session' => $session]),
        );

        $idToken = $mfaResp->getIdToken();
        $apiKey = $mfaResp->getApiKey() ?? $this->env('WSAPI_TEST_APIKEY');
        if (!$idToken || !$apiKey) {
            $this->fail("admin MFA did not return idToken+apiKey");
        }
        return [$idToken, $apiKey];
    }
}
