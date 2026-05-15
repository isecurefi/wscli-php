<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Session;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\LoginMFAReq;
use IsecureFi\WsApi\Client\Model\LoginReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'session:login', description: 'Login (data mode: password only; admin mode: password + SMS MFA).')]
final class LoginCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Account password (>=20 chars).')
            ->addOption('code',     null, InputOption::VALUE_REQUIRED, 'SMS MFA code (admin mode).')
            ->addOption('session',  null, InputOption::VALUE_REQUIRED, 'MFA session token from a prior login leg.')
            ->addOption('noninteractive', null, InputOption::VALUE_NONE, 'Never prompt; fail if input is missing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string)($this->opts['email'] ?? '');
        $mode  = strtolower((string)($this->opts['mode'] ?? ''));
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

            $resp = $this->sessionApi()->login(
                $email,
                $mode,
                new LoginReq(['chResp' => $challengeStr, 'encrypted' => $encrypted]),
            );

            // Admin path -> additional MFA leg.
            if ($resp->getSession()) {
                $code = $this->resolveMfaCode($input, $output, (string)$resp->getSession());
                if ($code === null) {
                    return self::FAILURE;
                }
                $resp = $this->sessionApi()->loginMFA(
                    $email,
                    'admin',
                    new LoginMFAReq(['code' => $code, 'session' => (string)$resp->getSession()]),
                );
            }
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }

        $this->persist($input, [
            'email'      => $email,
            'mode'       => $mode,
            'idtoken'    => $resp->getIdToken(),
            'apikey'     => $resp->getApiKey(),
            'expiresin'  => $resp->getExpiresIn(),
        ]);

        return $this->emit($input, $output, [
            'idtoken' => $resp->getIdToken(),
            'apikey'  => $resp->getApiKey(),
            'expiresIn' => $resp->getExpiresIn(),
        ]);
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
        $q = (new Question('Password: '))->setHidden(true)->setHiddenFallback(false);
        return $helper->ask($input, $output, $q);
    }

    private function resolveMfaCode(InputInterface $input, OutputInterface $output, string $session): ?string
    {
        $code = $this->opts['code'] ?? null;
        if (is_string($code) && $code !== '') {
            return $code;
        }
        if ($input->getOption('noninteractive') || ! $input->isInteractive()) {
            $output->writeln(sprintf('<error>MFA required. Session token: %s. Pass --code=<smscode>.</error>', $session));
            return null;
        }
        return $this->getHelper('question')->ask($input, $output, new Question('SMS code: '));
    }
}
