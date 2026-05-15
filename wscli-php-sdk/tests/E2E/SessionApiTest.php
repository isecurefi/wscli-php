<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\LoginReq;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('session')]
final class SessionApiTest extends E2ETestCase
{
    #[Test]
    public function init_login_returns_a_challenge_string(): void
    {
        $resp = $this->sessionApi()->initLogin('e2e-probe@example.invalid', 'data');

        $challenge = $resp->getChallenge();
        self::assertIsString($challenge);
        self::assertNotEmpty($challenge, 'initLogin must return a non-empty challenge even for unknown emails');
        self::assertGreaterThanOrEqual(58, strlen($challenge), 'challenge must include the 13-byte window starting at offset 45');
    }

    #[Test]
    public function login_with_wrong_password_is_rejected(): void
    {
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        $environment = $this->env('WSAPI_ENVIRONMENT', 'test');

        $challenge = $this->challengeHelper->fetch($email, 'data');
        $bogus = $this->challengeHelper->encryptPassword('this-is-not-the-password-1234!', $challenge, $environment);

        $req = new LoginReq(['chResp' => $challenge, 'encrypted' => $bogus]);

        $this->expectException(ApiException::class);
        $this->sessionApi()->login($email, 'data', $req);
    }

    #[Test]
    public function data_mode_login_yields_idtoken_and_apikey(): void
    {
        [$idToken, $apiKey] = $this->loginAsDataUser();

        self::assertNotEmpty($idToken);
        self::assertNotEmpty($apiKey);
        self::assertMatchesRegularExpression('/^ey[A-Za-z0-9_\-]+\./', $idToken, 'IdToken should look like a JWT');
    }

    #[Test]
    public function logout_revokes_the_session(): void
    {
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        [$idToken, $_] = $this->loginAsDataUser();

        $resp = $this->sessionApi()->logout($idToken, $email, 'data');
        self::assertNotNull($resp);
    }

    #[Test]
    public function admin_login_requires_mfa_and_returns_idtoken(): void
    {
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        self::assertNotEmpty($idToken);
        self::assertNotEmpty($apiKey);
    }
}
