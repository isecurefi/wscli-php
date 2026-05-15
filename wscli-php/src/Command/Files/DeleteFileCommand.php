<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Files;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'files:delete', description: 'Delete a single file by reference.')]
final class DeleteFileCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',          'b',  InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('filetype',      null, InputOption::VALUE_REQUIRED, 'Bank file type.')
            ->addOption('filereference', null, InputOption::VALUE_REQUIRED, 'Bank file reference.');
    }

    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank = (string)($this->opts['bank'] ?? '');
        $type = (string)($this->opts['filetype'] ?? '');
        $ref  = (string)($this->opts['filereference'] ?? '');
        if ($bank === '' || $type === '' || $ref === '') {
            $output->writeln('<error>--bank, --filetype, --filereference are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->filesApi()->deleteFile(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                $type,
                $ref,
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
