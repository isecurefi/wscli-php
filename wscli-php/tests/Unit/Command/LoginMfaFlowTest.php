<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Tests\Unit\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use IsecureFi\WsCli\Command\Session\LoginCommand;
use IsecureFi\WsCli\Command\Session\LoginMfaCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Yaml\Yaml;

final class LoginMfaFlowTest extends CommandTestCase
{
    public static function factors(): array
    {
        return [
            'authenticator' => ['SOFTWARE_TOKEN_MFA', 'Authenticator code:'],
            'sms' => ['SMS_MFA', 'SMS code:'],
            'legacy sms' => [null, 'SMS code:'],
        ];
    }

    #[DataProvider('factors')]
    public function test_login_echoes_challenge_and_prompts_for_correct_factor(?string $factor, string $prompt): void
    {
        $history = [];
        $challenge = ['ResponseCode' => '00', 'Session' => 'synthetic-session'];
        if ($factor !== null) {
            $challenge['ChallengeName'] = $factor;
        }
        $client = $this->client([
            ['Challenge' => str_repeat('x', 64)],
            $challenge,
            ['ResponseCode' => '00', 'IdToken' => 'synthetic-token', 'ApiKey' => 'synthetic-key'],
        ], $history);
        $command = new LoginCommand(null, $client);
        $command->setHelperSet(new HelperSet(['question' => new QuestionHelper()]));
        $tester = $this->tester($command);
        $tester->setInputs(['123456']);
        $exit = $tester->execute($this->withConfig([
            '--email' => 'test@example.invalid', '--mode' => 'admin', '--password' => 'Synthetic-password-123!',
        ]));
        self::assertSame(0, $exit, $tester->getDisplay());
        self::assertStringContainsString($prompt, $tester->getDisplay());
        $body = json_decode((string) $history[2]['request']->getBody(), true);
        self::assertSame($factor ?? 'SMS_MFA', $body['ChallengeName']);
        self::assertSame('synthetic-session', $body['Session']);
        self::assertSame('123456', $body['Code']);
        self::assertSame('synthetic-token', Yaml::parseFile($this->configPath)['settings']['idtoken']);
    }

    public function test_failed_mfa_does_not_overwrite_existing_session_and_returns_failure(): void
    {
        file_put_contents($this->configPath, "settings:\n  idtoken: existing-token\n  apikey: existing-key\n");
        $before = file_get_contents($this->configPath);
        $history = [];
        $client = $this->client([['ResponseCode' => '01', 'ResponseText' => 'User MFA authentication failed']], $history);
        $tester = $this->tester(new LoginMfaCommand(null, $client));
        $exit = $tester->execute($this->withConfig([
            '--email' => 'test@example.invalid', '--mode' => 'admin', '--code' => '123456',
            '--session' => 'synthetic-session', '--challenge-name' => 'SOFTWARE_TOKEN_MFA',
        ]));
        self::assertSame(1, $exit);
        self::assertSame($before, file_get_contents($this->configPath));
        $body = json_decode((string) $history[0]['request']->getBody(), true);
        self::assertSame('SOFTWARE_TOKEN_MFA', $body['ChallengeName']);
    }

    public static function rejectedResponses(): array
    {
        return [
            'failed password' => [['ResponseCode' => '01']],
            'missing session credentials' => [['ResponseCode' => '00']],
            'selection missing offered factors' => [['ResponseCode' => '00', 'Session' => 'private-session', 'ChallengeName' => 'SELECT_MFA_TYPE']],
            'unknown challenge' => [['ResponseCode' => '00', 'Session' => 'private-session', 'ChallengeName' => 'UNKNOWN']],
            'missing interactive code' => [['ResponseCode' => '00', 'Session' => 'private-session', 'ChallengeName' => 'SOFTWARE_TOKEN_MFA']],
        ];
    }

    #[DataProvider('rejectedResponses')]
    public function test_rejected_or_incomplete_login_preserves_settings_without_exposing_session(array $response): void
    {
        $before = file_get_contents($this->configPath);
        $history = [];
        $client = $this->client([['Challenge' => str_repeat('x', 64)], $response], $history);
        $tester = $this->tester(new LoginCommand(null, $client));
        $exit = $tester->execute($this->withConfig([
            '--email' => 'test@example.invalid', '--mode' => 'admin',
            '--password' => 'Synthetic-password-123!', '--noninteractive' => true,
        ]));
        self::assertNotSame(0, $exit);
        self::assertSame($before, file_get_contents($this->configPath));
        self::assertStringNotContainsString('private-session', $tester->getDisplay());
        self::assertCount(2, $history);
    }

    public function test_data_login_still_saves_session_without_mfa(): void
    {
        $history = [];
        $client = $this->client([
            ['Challenge' => str_repeat('x', 64)],
            ['ResponseCode' => '00', 'IdToken' => 'data-token', 'ApiKey' => 'synthetic-key'],
        ], $history);
        $tester = $this->tester(new LoginCommand(null, $client));
        self::assertSame(0, $tester->execute($this->withConfig([
            '--email' => 'test@example.invalid', '--mode' => 'data',
            '--password' => 'Synthetic-password-123!', '--noninteractive' => true,
        ])));
        self::assertCount(2, $history);
        self::assertSame('data-token', Yaml::parseFile($this->configPath)['settings']['idtoken']);
    }

    public static function selections(): array
    {
        return [
            'default authenticator' => [null, ['', '123456'], 'SOFTWARE_TOKEN_MFA', false],
            'interactive sms' => [null, ['1', '123456'], 'SMS_MFA', false],
            'explicit totp' => ['totp', [], 'SOFTWARE_TOKEN_MFA', true],
            'explicit sms' => ['sms', [], 'SMS_MFA', true],
        ];
    }

    #[DataProvider('selections')]
    public function test_selects_factor_and_uses_fresh_session(?string $choice, array $inputs, string $factor, bool $noninteractive): void
    {
        $history = [];
        $client = $this->client([
            ['Challenge' => str_repeat('x', 64)],
            ['ResponseCode' => '00', 'Session' => 'selection-session', 'ChallengeName' => 'SELECT_MFA_TYPE', 'MfaOptions' => ['SMS_MFA', 'SOFTWARE_TOKEN_MFA']],
            ['ResponseCode' => '00', 'Session' => 'fresh-factor-session', 'ChallengeName' => $factor],
            ['ResponseCode' => '00', 'IdToken' => 'admin-token', 'ApiKey' => 'synthetic-key'],
        ], $history);
        $command = new LoginCommand(null, $client);
        $command->setHelperSet(new HelperSet(['question' => new QuestionHelper()]));
        $tester = $this->tester($command);
        $tester->setInputs($inputs);
        $options = ['--email' => 'test+admin@example.invalid', '--mode' => 'admin', '--password' => 'Synthetic-password-123!'];
        if ($choice !== null) {
            $options['--mfa-type'] = $choice;
        }
        if ($noninteractive) {
            $options['--noninteractive'] = true;
            $options['--code'] = '123456';
        }
        self::assertSame(0, $tester->execute($this->withConfig($options)), $tester->getDisplay());
        self::assertSame('PUT', $history[2]['request']->getMethod());
        self::assertSame('/session/test%2Badmin%40example.invalid/admin/selectmfa', $history[2]['request']->getUri()->getPath());
        self::assertSame(['MfaType' => $factor, 'Session' => 'selection-session'], json_decode((string) $history[2]['request']->getBody(), true));
        self::assertSame(['Code' => '123456', 'Session' => 'fresh-factor-session', 'ChallengeName' => $factor], json_decode((string) $history[3]['request']->getBody(), true));
        self::assertSame('admin-token', Yaml::parseFile($this->configPath)['settings']['idtoken']);
        self::assertStringNotContainsString('fresh-factor-session', $tester->getDisplay());
    }

    public static function invalidSelections(): array
    {
        return [
            'unoffered method' => [['SMS_MFA'], 'totp', null, 2],
            'unknown method' => [['SOFTWARE_TOKEN_MFA'], 'unknown', null, 2],
            'no supported methods' => [['UNKNOWN'], 'totp', null, 2],
            'malformed options' => ['SOFTWARE_TOKEN_MFA', 'totp', null, 2],
            'noninteractive without choice' => [['SMS_MFA', 'SOFTWARE_TOKEN_MFA'], null, null, 2],
            'selection rejected' => [['SOFTWARE_TOKEN_MFA'], 'totp', ['ResponseCode' => '01'], 3],
            'missing new session' => [['SOFTWARE_TOKEN_MFA'], 'totp', ['ResponseCode' => '00', 'ChallengeName' => 'SOFTWARE_TOKEN_MFA'], 3],
            'unexpected factor' => [['SOFTWARE_TOKEN_MFA'], 'totp', ['ResponseCode' => '00', 'ChallengeName' => 'SMS_MFA', 'Session' => 'private-session'], 3],
        ];
    }

    #[DataProvider('invalidSelections')]
    public function test_invalid_selection_preserves_settings(mixed $offered, ?string $choice, ?array $response, int $requestCount): void
    {
        $before = file_get_contents($this->configPath);
        $history = [];
        $responses = [
            ['Challenge' => str_repeat('x', 64)],
            ['ResponseCode' => '00', 'Session' => 'private-session', 'ChallengeName' => 'SELECT_MFA_TYPE', 'MfaOptions' => $offered],
        ];
        if ($response !== null) {
            $responses[] = $response;
        }
        $tester = $this->tester(new LoginCommand(null, $this->client($responses, $history)));
        $options = [
            '--email' => 'test@example.invalid', '--mode' => 'admin',
            '--password' => 'Synthetic-password-123!', '--noninteractive' => true, '--code' => '123456',
        ];
        if ($choice !== null) {
            $options['--mfa-type'] = $choice;
        }
        self::assertNotSame(0, $tester->execute($this->withConfig($options)));
        self::assertCount($requestCount, $history);
        self::assertSame($before, file_get_contents($this->configPath));
        self::assertStringNotContainsString('private-session', $tester->getDisplay());
    }

    private function client(array $responses, array &$history): Client
    {
        $handler = HandlerStack::create(new MockHandler(array_map(
            static fn(array $body): Response => new Response(200, ['Content-Type' => 'application/json'], json_encode($body)),
            $responses,
        )));
        $handler->push(Middleware::history($history));
        return new Client(['handler' => $handler]);
    }
}
