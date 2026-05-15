<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests\E2E;

use IsecureFi\WsApi\Client\Model\UploadFileReq;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[Group('e2e')]
#[Group('files')]
final class FilesApiTest extends E2ETestCase
{
    private string $bank;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bank = $this->env('WSAPI_TEST_BANK', 'nordea');
    }

    #[Test]
    public function list_files_returns_a_descriptor_collection(): void
    {
        [$idToken, $apiKey] = $this->loginAsDataUser();

        $resp = $this->filesApi()->listFiles($idToken, $apiKey, $this->bank);
        self::assertNotNull($resp);
        $files = $resp->getFiles();
        self::assertIsArray($files, 'listFiles returns a Files array (may be empty)');
    }

    #[Test]
    public function upload_then_download_then_delete_round_trip(): void
    {
        if ($this->env('WSAPI_TEST_ALLOW_FILE_UPLOAD') !== '1') {
            $this->markTestSkipped('Set WSAPI_TEST_ALLOW_FILE_UPLOAD=1 to exercise a real file upload+delete cycle.');
        }
        [$idToken, $apiKey] = $this->loginAsDataUser();

        $fileType = $this->env('WSAPI_TEST_FILETYPE', 'pain.001.001.02');
        $fileName = 'e2e-upload-' . date('Ymd-His') . '.xml';
        $contents = base64_encode("<e2e probe payload " . bin2hex(random_bytes(8)) . ">");

        $upload = $this->filesApi()->uploadFile(
            $idToken,
            $apiKey,
            $this->bank,
            new UploadFileReq([
                'fileName' => $fileName,
                'fileType' => $fileType,
                'fileContents' => $contents,
                'signature' => '',
            ]),
        );
        self::assertNotNull($upload);
        $ref = $upload->getFileReference();
        self::assertNotEmpty($ref, 'uploadFile must return a fileReference');

        try {
            $download = $this->filesApi()->downloadFile($idToken, $apiKey, $this->bank, $fileType, $ref);
            self::assertSame($contents, $download->getFileContents(), 'downloaded contents match what we uploaded');
        } finally {
            $delete = $this->filesApi()->deleteFile($idToken, $apiKey, $this->bank, $fileType, $ref);
            self::assertNotNull($delete);
        }
    }
}
