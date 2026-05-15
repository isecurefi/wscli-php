<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:share', description: 'Share this account\'s certs with a peer account on the same API key.')]
final class ShareCertsCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this->addOption('extemail', null, InputOption::VALUE_REQUIRED, 'Peer account email.');
    }

    protected function requiresAuthenticatedSession(): bool { return true; }
    protected function requiresAdminMode(): bool            { return true; }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extEmail = (string)($this->opts['extemail'] ?? '');
        if ($extEmail === '') {
            $output->writeln('<error>--extemail is required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->certsApi()->shareCerts(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $extEmail,
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
