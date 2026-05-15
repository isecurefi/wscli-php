<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:export', description: 'Export the private cert material PGP-encrypted to the given key.')]
final class ExportCertCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',     'b', InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('pgpkeyid', null, InputOption::VALUE_REQUIRED, 'PGP key id (uploaded via pgp:upload-key) used as wrap target.');
    }

    protected function requiresAuthenticatedSession(): bool { return true; }
    protected function requiresAdminMode(): bool            { return true; }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank = (string)($this->opts['bank'] ?? '');
        $pgpKeyId = (string)($this->opts['pgpkeyid'] ?? '');
        if ($bank === '' || $pgpKeyId === '') {
            $output->writeln('<error>--bank and --pgpkeyid are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->certsApi()->exportCert(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                $pgpKeyId,
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
