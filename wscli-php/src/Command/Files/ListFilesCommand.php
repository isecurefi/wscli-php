<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Files;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'files:list', description: 'List files at the bank for the given filetype/status.')]
final class ListFilesCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',       'b', InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('filetype',   null, InputOption::VALUE_REQUIRED, 'Bank file type (e.g. KTL).')
            ->addOption('filestatus', null, InputOption::VALUE_REQUIRED, 'Bank file status (e.g. NEW, ALL).')
            ->addOption('startdate', null, InputOption::VALUE_REQUIRED, 'Inclusive start date (YYYY-MM-DD).')
            ->addOption('enddate', null, InputOption::VALUE_REQUIRED, 'Inclusive end date (YYYY-MM-DD).');
    }

    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank = (string)($this->opts['bank'] ?? '');
        if ($bank === '') {
            $output->writeln('<error>--bank is required.</error>');
            return self::INVALID;
        }
        $status   = (string)($this->opts['filestatus'] ?? '') ?: null;
        $fileType = (string)($this->opts['filetype']   ?? '') ?: null;

        try {
            $resp = $this->filesApi()->listFiles(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                $status,
                $fileType,
                startDate: isset($this->opts['startdate']) ? (string) $this->opts['startdate'] : null,
                endDate: isset($this->opts['enddate']) ? (string) $this->opts['enddate'] : null,
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp, $resp->getResponseCode() === '00' ? self::SUCCESS : self::FAILURE);
    }
}
