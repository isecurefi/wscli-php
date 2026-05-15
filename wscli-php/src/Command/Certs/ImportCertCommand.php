<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\ImportCertReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:import', description: 'Import an existing PEM cert/key pair (admin mode).')]
final class ImportCertCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',           'b', InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('wsuserid',        null, InputOption::VALUE_REQUIRED, 'WS user id.')
            ->addOption('company',         null, InputOption::VALUE_REQUIRED, 'Company name.')
            ->addOption('certificate',     null, InputOption::VALUE_REQUIRED, 'Signing PEM certificate.')
            ->addOption('privatekey',      null, InputOption::VALUE_REQUIRED, 'Signing PEM private key.')
            ->addOption('enccertificate',  null, InputOption::VALUE_REQUIRED, 'Encryption PEM certificate (optional).')
            ->addOption('encprivatekey',   null, InputOption::VALUE_REQUIRED, 'Encryption PEM private key (optional).');
    }

    protected function requiresAuthenticatedSession(): bool { return true; }
    protected function requiresAdminMode(): bool            { return true; }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank = (string)($this->opts['bank'] ?? '');
        $wsUserId = (string)($this->opts['wsuserid'] ?? '');
        $cert = (string)($this->opts['certificate'] ?? '');
        $key  = (string)($this->opts['privatekey']  ?? '');
        if ($bank === '' || $wsUserId === '' || $cert === '' || $key === '') {
            $output->writeln('<error>--bank, --wsuserid, --certificate, --privatekey are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->certsApi()->importCert(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                new ImportCertReq([
                    'certificate'    => $cert,
                    'privateKey'     => $key,
                    'wsUserId'       => $wsUserId,
                    'company'        => (string)($this->opts['company']        ?? ''),
                    'encCertificate' => (string)($this->opts['enccertificate'] ?? ''),
                    'encPrivatekey'  => (string)($this->opts['encprivatekey']  ?? ''),
                ]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
