<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Account;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'account:init-register', description: 'Fetch a fresh registration challenge.')]
final class InitRegisterCommand extends AbstractWsCliCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string)($this->opts['email'] ?? '');
        $mode  = (string)($this->opts['mode']  ?? '');
        if ($email === '' || $mode === '') {
            $output->writeln('<error>--email and --mode are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->accountApi()->initRegister($email, $mode);
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, ['challenge' => $resp->getChallenge()]);
    }
}
