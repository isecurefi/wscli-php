<?php

declare(strict_types=1);

namespace IsecureFi\WsApi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as HttpResponse;
use IsecureFi\WsApi\Client\Api\FilesApi;
use IsecureFi\WsApi\Client\Model\Response;
use IsecureFi\WsApi\Client\Model\UploadFileReq;
use IsecureFi\WsApi\Client\Model\UploadFileResp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UploadFileResponseTest extends TestCase
{
    public static function responses(): iterable
    {
        foreach (['uploadFile', 'uploadFileWithHttpInfo', 'uploadFileAsync', 'uploadFileAsyncWithHttpInfo'] as $method) {
            yield "$method inline" => [$method, base64_encode('<Document>saldo 1450.05</Document>')];
            yield "$method acknowledgement" => [$method, null];
        }
    }

    #[DataProvider('responses')]
    public function test_preserves_optional_content_with_one_upload(string $method, ?string $content): void
    {
        $body = ['ResponseCode' => '00', 'ResponseText' => 'OK'];
        if ($content !== null) {
            $body['Content'] = $content;
        }
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new HttpResponse(201, [], json_encode($body))]));
        $handler->push(Middleware::history($history));
        $api = new FilesApi(new Client(['handler' => $handler]));
        $request = new UploadFileReq(['fileName' => 'balance.xml', 'fileType' => 'camt.060.001.02',
            'fileContents' => base64_encode('<query/>'), 'signature' => 'synthetic-signature']);
        $result = $api->$method('synthetic-token', 'synthetic-key', 'osuuspankki', $request);
        if (str_contains($method, 'Async')) {
            $result = $result->wait();
        }
        if (str_contains($method, 'WithHttpInfo')) {
            self::assertSame(201, $result[1]);
            $result = $result[0];
        }
        self::assertInstanceOf(UploadFileResp::class, $result);
        self::assertInstanceOf(Response::class, $result);
        self::assertSame('00', $result->getResponseCode());
        self::assertSame($content, $result->getContent());
        self::assertSame($body, json_decode(json_encode($result), true));
        self::assertTrue($result->valid());
        self::assertCount(1, $history);
        self::assertSame('PUT', $history[0]['request']->getMethod());
        self::assertStringEndsWith('/files/osuuspankki', $history[0]['request']->getUri()->getPath());
        self::assertSame('synthetic-signature', json_decode((string)$history[0]['request']->getBody(), true)['Signature']);
    }
}
