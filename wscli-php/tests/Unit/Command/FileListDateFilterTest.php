<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use IsecureFi\WsApi\Client\Api\FilesApi;
use IsecureFi\WsCli\Command\Files\ListFilesCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;

final class FileListDateFilterTest extends CommandTestCase
{
    public static function ranges(): iterable
    {
        yield 'unchanged' => [[], []];
        yield 'bounded' => [['--startdate' => '2026-11-08', '--enddate' => '2026-11-10'], ['StartDate' => '2026-11-08', 'EndDate' => '2026-11-10']];
        yield 'start only' => [['--startdate' => '2026-11-08'], ['StartDate' => '2026-11-08']];
        yield 'end only' => [['--enddate' => '2026-11-10'], ['EndDate' => '2026-11-10']];
        yield 'leap day' => [['--startdate' => '2028-02-29', '--enddate' => '2028-02-29'], ['StartDate' => '2028-02-29', 'EndDate' => '2028-02-29']];
    }

    #[DataProvider('ranges')]
    public function test_dates_reach_http_request(array $options, array $dates): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(200, [], '{"ResponseCode":"00","ResponseText":"OK."}')]));
        $handler->push(Middleware::history($history));
        $tester = $this->tester(new ListFilesCommand(null, new Client(['handler' => $handler])));
        self::assertSame(Command::SUCCESS, $tester->execute($this->withConfig($options + $this->options())), $tester->getDisplay());
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        parse_str($request->getUri()->getQuery(), $query);
        self::assertSame(['Status' => 'ALL', 'FileType' => 'pain.002.001.02'] + $dates, $query);
        self::assertSame('/files/osuuspankki', $request->getUri()->getPath());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('synthetic-token', $request->getHeaderLine('Authorization'));
        self::assertSame('00', json_decode($tester->getDisplay(), true)['ResponseCode']);
    }

    public static function invalidRanges(): iterable
    {
        foreach (['2026-02-29', '2026-11-31', '2026-1-01', '', '2026-11-08T00:00:00Z'] as $date) {
            yield $date => [['--startdate' => $date]];
        }
        yield 'reversed' => [['--startdate' => '2026-11-10', '--enddate' => '2026-11-08']];
        yield 'invalid end' => [['--enddate' => 'not-a-date']];
    }

    #[DataProvider('invalidRanges')]
    public function test_invalid_dates_never_send(array $options): void
    {
        $tester = $this->tester(new ListFilesCommand(null, new Client(['handler' => HandlerStack::create(new MockHandler())])));
        self::assertSame(Command::INVALID, $tester->execute($this->withConfig($options + $this->options())));
    }

    public function test_bank_error_is_not_a_successful_empty_list(): void
    {
        $body = ['ResponseCode' => '01', 'ResponseText' => 'Bank outcome unknown; reconcile before retrying'];
        $handler = HandlerStack::create(new MockHandler([new Response(200, [], json_encode($body))]));
        $tester = $this->tester(new ListFilesCommand(null, new Client(['handler' => $handler])));
        self::assertSame(Command::FAILURE, $tester->execute($this->withConfig($this->options())));
        self::assertSame($body, json_decode($tester->getDisplay(), true));
    }

    public function test_async_sdk_passes_dates_and_preserves_content_type_argument(): void
    {
        $history = [];
        $handler = HandlerStack::create(new MockHandler([new Response(200, [], '{"ResponseCode":"00","ResponseText":"OK."}')]));
        $handler->push(Middleware::history($history));
        $api = new FilesApi(new Client(['handler' => $handler]));
        $api->listFilesAsync('synthetic-token', 'synthetic-key', 'osuuspankki', 'ALL', 'pain.002.001.02', FilesApi::contentTypes['listFiles'][0], '2026-11-08', '2026-11-10')->wait();
        parse_str($history[0]['request']->getUri()->getQuery(), $query);
        self::assertSame('2026-11-08', $query['StartDate']);
        self::assertSame('2026-11-10', $query['EndDate']);
    }

    private function options(): array
    {
        return ['--idtoken' => 'synthetic-token', '--apikey' => 'synthetic-key', '--bank' => 'osuuspankki',
            '--filetype' => 'pain.002.001.02', '--filestatus' => 'ALL', '--json' => true];
    }
}
