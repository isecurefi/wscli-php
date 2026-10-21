<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Session;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\LoginReq;
use IsecureFi\WsCli\Auth\SessionLogin;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'session:login', description: 'Login (data mode: password only; admin mode: password + SMS or authenticator MFA).')]
final class LoginCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Account password (>=20 chars).')
            ->addOption('code', null, InputOption::VALUE_REQUIRED, 'SMS or authenticator verification code (admin mode).')
            ->addOption('mfa-type', null, InputOption::VALUE_REQUIRED, 'Choose totp or sms when the server offers MFA selection.')
            ->addOption('session', null, InputOption::VALUE_REQUIRED, 'MFA session token from a prior login leg.')
            ->addOption('noninteractive', null, InputOption::VALUE_NONE, 'Never prompt; fail if input is missing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string) ($this->opts['email'] ?? '');
        $mode  = strtolower((string) ($this->opts['mode'] ?? ''));
        if ($email === '' || !in_array($mode, ['admin', 'data'], true)) {
            $output->writeln('<error>--email and --mode (admin|data) are required.</error>');
            return self::INVALID;
        }
        $password = $this->resolvePassword($input, $output);
        if ($password === null) {
            return self::FAILURE;
        }
        $environment = $this->envResolver->normalizeForChallenge($this->opts['environment'] ?? null);

        try {
            $challengeStr = $this->challenge->fetch($email, $mode);
            $encrypted    = $this->challenge->encryptPassword($password, $challengeStr, $environment);

            $login = new SessionLogin($this->config, $this->httpClient);
            $resp = $login->login(
                $email,
                $mode,
                new LoginReq(['chResp' => $challengeStr, 'encrypted' => $encrypted]),
            );

            // Admin path -> additional MFA leg.
            if (!empty($resp['Session'])) {
                if (($resp['ChallengeName'] ?? null) === 'SELECT_MFA_TYPE') {
                    $selected = $this->resolveMfaType($input, $output, $resp['MfaOptions'] ?? []);
                    $resp = $login->selectFactor($email, (string) $resp['Session'], $selected);
                }
                $factor = SessionLogin::factor($resp['ChallengeName'] ?? 'SMS_MFA');
                $code = $this->resolveMfaCode($input, $output, $factor);
                if ($code === null) {
                    return self::FAILURE;
                }
                $resp = $login->mfa($email, $code, (string) $resp['Session'], $factor);
            }
            if (empty($resp['AccessToken'])) {
                SessionLogin::requireSession($resp);
            }
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::FAILURE;
        }

        // When the account exists but its email isn't verified yet, the API
        // returns 2xx with an `accessToken` but no `idToken`/`apiKey`. The
        // user must then call `account:verify-email` with that token. Surface
        // it here so the operator (or test harness) can pick it up.
        $this->persist($input, [
            'email'       => $email,
            'mode'        => $mode,
            'idtoken'     => ($resp['IdToken'] ?? null),
            'apikey'      => ($resp['ApiKey'] ?? null),
            'expiresin'   => ($resp['ExpiresIn'] ?? null),
            'accesstoken' => ($resp['AccessToken'] ?? null),
        ]);

        return $this->emit($input, $output, [
            'idtoken'     => ($resp['IdToken'] ?? null),
            'apikey'      => ($resp['ApiKey'] ?? null),
            'expiresIn'   => ($resp['ExpiresIn'] ?? null),
            'accessToken' => ($resp['AccessToken'] ?? null),
            'responseCode' => ($resp['ResponseCode'] ?? null),
            'responseText' => ($resp['ResponseText'] ?? null),
        ]);
    }

    private function resolveMfaType(InputInterface $input, OutputInterface $output, mixed $offered): string
    {
        $methods = ['totp' => 'SOFTWARE_TOKEN_MFA', 'sms' => 'SMS_MFA'];
        $available = is_array($offered)
            ? array_filter($methods, static fn(string $factor): bool => in_array($factor, $offered, true))
            : [];
        if ($available === []) {
            throw new \RuntimeException('The server offered no supported MFA methods; settings were not changed.');
        }
        $choice = $this->opts['mfa-type'] ?? null;
        if ($choice !== null) {
            if (!is_string($choice) || !isset($available[$choice])) {
                throw new \InvalidArgumentException('--mfa-type must be totp or sms and offered by the server.');
            }
            return $available[$choice];
        }
        if ($input->getOption('noninteractive') || !$input->isInteractive()) {
            throw new \RuntimeException('MFA selection required. Provide --mfa-type=totp or --mfa-type=sms.');
        }
        $labels = [];
        foreach ($available as $method => $factor) {
            $labels[$factor] = $method === 'totp' ? 'Authenticator (TOTP)' : 'SMS';
        }
        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        $question = new ChoiceQuestion('Verification method:', array_values($labels), 0);
        $question->setMaxAttempts(3);
        $answer = $helper->ask($input, $output, $question);
        $factor = array_search($answer, $labels, true);
        if ($factor === false) {
            throw new \RuntimeException('No MFA method selected; settings were not changed.');
        }
        return $factor;
    }

    private function resolvePassword(InputInterface $input, OutputInterface $output): ?string
    {
        $pw = $this->opts['password'] ?? null;
        if (is_string($pw) && $pw !== '') {
            return $pw;
        }
        if ($input->getOption('noninteractive') || ! $input->isInteractive()) {
            $output->writeln('<error>--password missing (set on CLI, in settings.yaml, or omit --noninteractive).</error>');
            return null;
        }
        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        $q = (new Question('Password: '))->setHidden(true)->setHiddenFallback(false);
        return $helper->ask($input, $output, $q);
    }

    private function resolveMfaCode(InputInterface $input, OutputInterface $output, string $factor): ?string
    {
        $code = $this->opts['code'] ?? null;
        if (is_string($code) && $code !== '') {
            return $code;
        }
        if ($input->getOption('noninteractive') || ! $input->isInteractive()) {
            $output->writeln('<error>MFA required. Run interactively or provide --code for the required factor.</error>');
            return null;
        }
        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        return $helper->ask($input, $output, (new Question($factor === 'SOFTWARE_TOKEN_MFA' ? 'Authenticator code: ' : 'SMS code: '))->setHidden(true)->setHiddenFallback(false));
    }
}
