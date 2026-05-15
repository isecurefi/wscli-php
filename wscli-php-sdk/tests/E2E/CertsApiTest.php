<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Client\Model\ConfigCertsReq;
use IsecureFi\WsApi\Client\Model\EnrollCertReq;
use IsecureFi\WsApi\Client\Model\ImportCertReq;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('certs')]
final class CertsApiTest extends E2ETestCase
{
    #[Test]
    public function list_certs_returns_a_collection(): void
    {
        [$idToken, $apiKey] = $this->loginAsDataUser();

        $resp = $this->certsApi()->listCerts($idToken, $apiKey);
        self::assertNotNull($resp);
        $certs = $resp->getCerts();
        self::assertIsArray($certs);
    }

    #[Test]
    public function config_certs_toggles_export_flag(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_CERT_CONFIG') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_CERT_CONFIG=1 to toggle Export flag on the test account.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();

        $resp = $this->certsApi()->configCerts(
            $idToken,
            $apiKey,
            new ConfigCertsReq(['export' => false]),
        );
        self::assertNotNull($resp);
    }

    #[Test]
    public function enroll_cert_is_opt_in(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_CERT_ENROLL') !== '1') {
            $this->markTestSkipped('EnrollCert hits a real bank backend. Set WSAPI_TEST_ALLOW_CERT_ENROLL=1 to run.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        $bank = $this->requireEnv('WSAPI_TEST_BANK');
        $pincode = $this->requireEnv('WSAPI_TEST_BANK_PINCODE');
        $wsUserId = $this->requireEnv('WSAPI_TEST_WSUSERID');

        $resp = $this->certsApi()->enrollCert(
            $idToken,
            $apiKey,
            $bank,
            new EnrollCertReq([
                'code' => $pincode,
                'company' => $this->env('WSAPI_TEST_NEW_COMPANY', 'E2E Test Oy'),
                'wsUserId' => $wsUserId,
            ]),
        );
        self::assertNotNull($resp);
    }

    #[Test]
    public function import_cert_is_opt_in(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_CERT_IMPORT') !== '1') {
            $this->markTestSkipped('ImportCert stores real key material. Set WSAPI_TEST_ALLOW_CERT_IMPORT=1 to run.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        $bank = $this->requireEnv('WSAPI_TEST_BANK');

        $resp = $this->certsApi()->importCert(
            $idToken,
            $apiKey,
            $bank,
            new ImportCertReq([
                'certificate' => $this->requireEnv('WSAPI_TEST_CERT_PEM'),
                'privateKey' => $this->requireEnv('WSAPI_TEST_KEY_PEM'),
                'wsUserId' => $this->requireEnv('WSAPI_TEST_WSUSERID'),
                'company' => $this->env('WSAPI_TEST_NEW_COMPANY', 'E2E Test Oy'),
            ]),
        );
        self::assertNotNull($resp);
    }

    #[Test]
    public function export_cert_is_opt_in(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_CERT_EXPORT') !== '1') {
            $this->markTestSkipped('ExportCert returns PGP-wrapped private key material. Opt in via WSAPI_TEST_ALLOW_CERT_EXPORT=1.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        $bank = $this->requireEnv('WSAPI_TEST_BANK');
        $pgpKeyId = $this->requireEnv('WSAPI_TEST_EXPORT_PGP_KEYID');

        $resp = $this->certsApi()->exportCert($idToken, $apiKey, $bank, $pgpKeyId);
        self::assertNotNull($resp);
    }

    #[Test]
    public function share_then_unshare_round_trip(): void
    {
        if ($this->env('WSAPI_TEST_EXT_EMAIL') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_EXT_EMAIL to a peer account on the same API key to exercise share/unshare.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        $extEmail = $this->requireEnv('WSAPI_TEST_EXT_EMAIL');

        $share = $this->certsApi()->shareCerts($idToken, $apiKey, $extEmail);
        self::assertNotNull($share);

        $unshare = $this->certsApi()->unshareCerts($idToken, $apiKey, $extEmail);
        self::assertNotNull($unshare);
    }
}
