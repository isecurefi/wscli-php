<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Files;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\UploadFileReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'files:upload', description: 'Upload a single file to the bank.')]
final class UploadFileCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',         'b',  InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('filetype',     null, InputOption::VALUE_REQUIRED, 'Bank file type.')
            ->addOption('filename',     null, InputOption::VALUE_REQUIRED, 'Logical file name.')
            ->addOption('filepath',     null, InputOption::VALUE_REQUIRED, 'Local path to file. Mutually exclusive with --filecontents.')
            ->addOption('filecontents', null, InputOption::VALUE_REQUIRED, 'Base64 file contents (alternative to --filepath).')
            ->addOption('signatures',   null, InputOption::VALUE_REQUIRED, 'PGP detached signatures (newline-separated).');
    }

    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank     = (string)($this->opts['bank']     ?? '');
        $type     = (string)($this->opts['filetype'] ?? '');
        $name     = (string)($this->opts['filename'] ?? '');
        $path     = (string)($this->opts['filepath'] ?? '');
        $contents = (string)($this->opts['filecontents'] ?? '');
        $sig      = (string)($this->opts['signatures']   ?? '');

        if ($bank === '' || $type === '' || $name === '') {
            $output->writeln('<error>--bank, --filetype and --filename are required.</error>');
            return self::INVALID;
        }
        if ($path !== '' && $contents !== '') {
            $output->writeln('<error>--filepath and --filecontents are mutually exclusive.</error>');
            return self::INVALID;
        }
        if ($path === '' && $contents === '') {
            $output->writeln('<error>One of --filepath or --filecontents is required.</error>');
            return self::INVALID;
        }
        if ($path !== '') {
            if (!is_file($path) || !is_readable($path)) {
                $output->writeln("<error>Cannot read --filepath: $path</error>");
                return self::FAILURE;
            }
            $contents = base64_encode((string)file_get_contents($path));
        }

        try {
            $resp = $this->filesApi()->uploadFile(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                new UploadFileReq([
                    'fileName'     => $name,
                    'fileType'     => $type,
                    'fileContents' => $contents,
                    'signature'    => $sig,
                ]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
