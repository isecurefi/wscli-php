<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Account;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\VerifyEmailReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'account:verify-email', description: 'Submit the email verification code.')]
final class VerifyEmailCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('code',        null, InputOption::VALUE_REQUIRED, '6-digit code emailed to the account.')
            ->addOption('accesstoken', null, InputOption::VALUE_REQUIRED, 'Access token returned by Register.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string)($this->opts['email'] ?? '');
        $mode  = (string)($this->opts['mode']  ?? '');
        $code  = (string)($this->opts['code']  ?? '');
        $token = (string)($this->opts['accesstoken'] ?? '');

        if ($email === '' || $mode === '' || $code === '' || $token === '') {
            $output->writeln('<error>--email, --mode, --code, --accesstoken are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->accountApi()->verifyEmail(
                $email,
                $mode,
                new VerifyEmailReq(['code' => $code, 'accessToken' => $token]),
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
