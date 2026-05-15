<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Integrator;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'integrator:list-accounts', description: 'List all accounts under the current API Key (owner-only).')]
final class ListAccountsCommand extends AbstractWsCliCommand
{
    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $resp = $this->integratorApi()->listAccounts(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
