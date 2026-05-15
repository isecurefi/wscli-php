<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Client\Model\DeleteKeyReq;
use IsecureFi\WsApi\Client\Model\UploadKeyReq;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('pgp')]
final class PgpApiTest extends E2ETestCase
{
    #[Test]
    public function list_keys_succeeds_for_authenticated_user(): void
    {
        [$idToken, $apiKey] = $this->loginAsDataUser();

        $resp = $this->pgpApi()->listKeys($idToken, $apiKey);
        self::assertNotNull($resp);
        $keys = $resp->getPgpKeys();
        self::assertIsArray($keys);
    }

    #[Test]
    public function upload_then_delete_round_trip(): void
    {
        if ($this->env('WSAPI_TEST_PGP_KEY') === null) {
            $this->markTestSkipped('Set WSAPI_TEST_PGP_KEY (armored public key) to exercise the upload/delete cycle.');
        }
        [$idToken, $apiKey] = $this->loginAsAdminUser();
        $armored = $this->requireEnv('WSAPI_TEST_PGP_KEY');
        $purpose = $this->env('WSAPI_TEST_PGP_PURPOSE', 'authorization');

        $upload = $this->pgpApi()->uploadKey(
            $idToken,
            $apiKey,
            new UploadKeyReq(['pgpKey' => $armored, 'pgpKeyPurpose' => $purpose]),
        );
        self::assertNotNull($upload);

        // Find the freshly uploaded key id and delete it.
        $list = $this->pgpApi()->listKeys($idToken, $apiKey);
        $latest = $list->getPgpKeys()[count($list->getPgpKeys()) - 1] ?? null;
        self::assertNotNull($latest, 'expected listKeys to include the freshly uploaded key');

        $resp = $this->pgpApi()->deleteKey(
            $idToken,
            $apiKey,
            new DeleteKeyReq(['pgpKeyId' => $latest->getPgpKeyId()]),
        );
        self::assertSame('00', $resp->getResponseCode());
    }
}
