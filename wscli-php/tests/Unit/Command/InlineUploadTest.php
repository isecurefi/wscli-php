<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use IsecureFi\WsCli\Command\Files\UploadFileCommand;
use PHPUnit\Framework\Attributes\DataProvider;

final class InlineUploadTest extends CommandTestCase
{
    public static function responses(): iterable
    {
        foreach ([true, false] as $json) {
            yield 'inline ' . (int)$json => [$json, ['ResponseCode' => '00', 'ResponseText' => 'OK', 'Content' => base64_encode('<report/>')]];
            yield 'ack ' . (int)$json => [$json, ['ResponseCode' => '00', 'ResponseText' => 'OK']];
            yield 'refusal ' . (int)$json => [$json, ['ResponseCode' => '01', 'ResponseText' => 'Unknown account']];
        }
    }

    #[DataProvider('responses')]
    public function test_upload_output_preserves_bank_response(bool $json, array $body): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(201, [], json_encode($body))]));
        $handler->push(Middleware::history($history));
        $tester = $this->tester(new UploadFileCommand(null, new Client(['handler' => $handler])));
        $options = ['--idtoken' => 'synthetic-token', '--apikey' => 'synthetic-key',
            '--bank' => 'osuuspankki', '--filetype' => 'camt.060.001.02', '--filename' => 'balance.xml',
            '--filecontents' => base64_encode('<query/>')];
        if ($json) {
            $options['--json'] = true;
        }
        self::assertSame(0, $tester->execute($this->withConfig($options)), $tester->getDisplay());
        self::assertSame($body, json_decode($tester->getDisplay(), true));
        self::assertCount(1, $history);
        self::assertSame('PUT', $history[0]['request']->getMethod());
    }
}
