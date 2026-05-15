<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\PasswordResetReq;
use IsecureFi\WsApi\Client\Model\RegisterReq;
use IsecureFi\WsApi\Client\Model\VerifyEmailReq;
use IsecureFi\WsApi\Client\Model\VerifyPhoneReq;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('account')]
final class AccountApiTest extends E2ETestCase
{
    #[Test]
    public function init_register_returns_a_challenge_for_an_unregistered_email(): void
    {
        // initRegister is non-mutating; safe to probe with a synthetic email.
        $resp = $this->accountApi()->initRegister('e2e-probe-' . uniqid() . '@example.invalid', 'data');
        $challenge = $resp->getChallenge();
        self::assertIsString($challenge);
        self::assertNotEmpty($challenge);
    }

    #[Test]
    public function init_password_reset_succeeds_for_known_email(): void
    {
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        // Read-only path: requests the server to send a code, but does not change the password.
        if ($this->env('WSAPI_TEST_ALLOW_PASSWORD_RESET_INIT') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_PASSWORD_RESET_INIT=1 to dispatch a real reset SMS.');
        }
        $resp = $this->accountApi()->initPasswordReset($email, 'data');
        self::assertNotNull($resp);
    }

    #[Test]
    public function register_is_opt_in(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_REGISTER') !== '1') {
            $this->markTestSkipped('Register creates state on the test backend. Set WSAPI_TEST_ALLOW_REGISTER=1 to run.');
        }
        $email = $this->requireEnv('WSAPI_TEST_NEW_EMAIL');
        $password = $this->requireEnv('WSAPI_TEST_NEW_PASSWORD');
        $phone = $this->requireEnv('WSAPI_TEST_NEW_PHONE');
        $apiKey = $this->env('WSAPI_TEST_APIKEY', '0');
        $environment = $this->env('WSAPI_ENVIRONMENT', 'test');

        $challenge = $this->challengeHelper->fetch($email, 'data', useRegister: true);
        $encrypted = $this->challengeHelper->encryptPassword($password, $challenge, $environment);

        $req = new RegisterReq([
            'chResp' => $challenge,
            'encrypted' => $encrypted,
            'apiKey' => $apiKey,
            'name' => $this->env('WSAPI_TEST_NEW_NAME', 'E2E Test User'),
            'company' => $this->env('WSAPI_TEST_NEW_COMPANY', 'E2E Test Oy'),
            'phone' => $phone,
        ]);
        try {
            $resp = $this->accountApi()->register($email, 'data', $req);
            self::assertNotNull($resp->getResponseCode());
        } catch (ApiException $e) {
            self::fail('register failed: ' . $e->getResponseBody());
        }
    }

    #[Test]
    public function verify_email_requires_a_code(): void
    {
        if ($this->env('WSAPI_TEST_VERIFY_EMAIL_CODE') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_VERIFY_EMAIL_CODE (and WSAPI_TEST_ACCESS_TOKEN) to verify a real email.');
        }
        $email = $this->requireEnv('WSAPI_TEST_NEW_EMAIL');
        $accessToken = $this->requireEnv('WSAPI_TEST_ACCESS_TOKEN');
        $code = $this->requireEnv('WSAPI_TEST_VERIFY_EMAIL_CODE');

        $resp = $this->accountApi()->verifyEmail(
            $email,
            'data',
            new VerifyEmailReq(['code' => $code, 'accessToken' => $accessToken]),
        );
        self::assertSame('00', $resp->getResponseCode(), 'expected response_code=00 on successful verify');
    }

    #[Test]
    public function verify_phone_requires_a_code(): void
    {
        if ($this->env('WSAPI_TEST_VERIFY_PHONE_CODE') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_VERIFY_PHONE_CODE to verify a real phone.');
        }
        $email = $this->requireEnv('WSAPI_TEST_NEW_EMAIL');
        $phone = $this->requireEnv('WSAPI_TEST_NEW_PHONE');
        $code = $this->requireEnv('WSAPI_TEST_VERIFY_PHONE_CODE');

        $resp = $this->accountApi()->verifyPhone(
            $email,
            'data',
            $phone,
            new VerifyPhoneReq(['code' => $code]),
        );
        self::assertSame('00', $resp->getResponseCode());
    }

    #[Test]
    public function password_reset_completes_with_sms_code(): void
    {
        if ($this->env('WSAPI_TEST_PASSWORD_RESET_CODE') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_PASSWORD_RESET_CODE (and WSAPI_TEST_NEW_PASSWORD) to complete a real reset.');
        }
        $email = $this->requireEnv('WSAPI_TEST_EMAIL');
        $newPassword = $this->requireEnv('WSAPI_TEST_NEW_PASSWORD');
        $code = $this->requireEnv('WSAPI_TEST_PASSWORD_RESET_CODE');
        $environment = $this->env('WSAPI_ENVIRONMENT', 'test');

        $challenge = $this->challengeHelper->fetch($email, 'data');
        $encrypted = $this->challengeHelper->encryptPassword($newPassword, $challenge, $environment);

        $resp = $this->accountApi()->passwordReset(
            $email,
            'data',
            new PasswordResetReq(['chResp' => $challenge, 'encrypted' => $encrypted, 'code' => $code]),
        );
        self::assertSame('00', $resp->getResponseCode());
    }
}
