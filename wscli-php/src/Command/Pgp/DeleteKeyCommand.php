<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Pgp;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\DeleteKeyReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'pgp:delete', description: 'Delete a PGP key by id.')]
final class DeleteKeyCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->addOption('pgpkeyid', null, InputOption::VALUE_REQUIRED, 'PGP key id from pgp:list.');
    }

    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }
    protected function requiresAdminMode(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keyId = (string)($this->opts['pgpkeyid'] ?? '');
        if ($keyId === '') {
            $output->writeln('<error>--pgpkeyid is required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->pgpApi()->deleteKey(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                new DeleteKeyReq(['pgpKeyId' => $keyId]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
