<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:list', description: 'List bank certificates available to the current account.')]
final class ListCertsCommand extends AbstractWsCliCommand
{
    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $resp = $this->certsApi()->listCerts(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
