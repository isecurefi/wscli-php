<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\ConfigCertsReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:config', description: 'Toggle whether certificates may be exported (admin mode).')]
final class ConfigCertsCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->addOption('export', null, InputOption::VALUE_REQUIRED, 'true|false');
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
        $exportRaw = (string)($this->opts['export'] ?? '');
        if (!in_array($exportRaw, ['true', 'false', '1', '0'], true)) {
            $output->writeln('<error>--export must be true|false.</error>');
            return self::INVALID;
        }
        $export = in_array($exportRaw, ['true', '1'], true);

        try {
            $resp = $this->certsApi()->configCerts(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                new ConfigCertsReq(['export' => $export]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
