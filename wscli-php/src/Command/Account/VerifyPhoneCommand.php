<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Account;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\VerifyPhoneReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'account:verify-phone', description: 'Submit the SMS phone verification code.')]
final class VerifyPhoneCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('phone', null, InputOption::VALUE_REQUIRED, 'Phone number used at registration.')
            ->addOption('code',  null, InputOption::VALUE_REQUIRED, '6-digit SMS code.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string)($this->opts['email'] ?? '');
        $mode  = (string)($this->opts['mode']  ?? '');
        $phone = (string)($this->opts['phone'] ?? '');
        $code  = (string)($this->opts['code']  ?? '');

        if ($email === '' || $mode === '' || $phone === '' || $code === '') {
            $output->writeln('<error>--email, --mode, --phone, --code are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->accountApi()->verifyPhone($email, $mode, $phone, new VerifyPhoneReq(['code' => $code]));
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, [
            'responseCode' => $resp->getResponseCode(),
            'responseText' => $resp->getResponseText(),
        ]);
    }
}
