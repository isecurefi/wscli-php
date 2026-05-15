<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Account;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\PasswordResetReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'account:password-reset', description: 'Complete a password reset with the SMS code and a new password.')]
final class PasswordResetCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'New password (>=20 chars).')
            ->addOption('code',     null, InputOption::VALUE_REQUIRED, 'Password-reset SMS code.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email   = (string)($this->opts['email']    ?? '');
        $mode    = (string)($this->opts['mode']     ?? '');
        $password = (string)($this->opts['password'] ?? '');
        $code    = (string)($this->opts['code']     ?? '');

        if ($email === '' || $mode === '' || $password === '' || $code === '') {
            $output->writeln('<error>--email, --mode, --password, --code are required.</error>');
            return self::INVALID;
        }
        if (strlen($password) < 20) {
            $output->writeln('<error>Password must be at least 20 characters.</error>');
            return self::INVALID;
        }

        $environment = $this->envResolver->normalizeForChallenge($this->opts['environment'] ?? null);

        try {
            $challengeStr = $this->challenge->fetch($email, $mode);
            $encrypted    = $this->challenge->encryptPassword($password, $challengeStr, $environment);
            $resp = $this->accountApi()->passwordReset(
                $email,
                $mode,
                new PasswordResetReq(['chResp' => $challengeStr, 'encrypted' => $encrypted, 'code' => $code]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, [
            'responseCode' => $resp->getResponseCode(),
            'responseText' => $resp->getResponseText(),
        ]);
    }
}
