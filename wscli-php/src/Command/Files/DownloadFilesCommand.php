<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Files;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Convenience wrapper: list matching files, then download each one to a sync dir.
 * This is the equivalent of the legacy `wscli files downloadFiles` aggregate.
 */
#[AsCommand(name: 'files:sync', description: 'Bulk download all files matching --filetype/--filestatus into --syncdir.')]
final class DownloadFilesCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',          'b',  InputOption::VALUE_REQUIRED, 'Bank slug.')
            ->addOption('filetype',      null, InputOption::VALUE_REQUIRED, 'Bank file type.')
            ->addOption('filestatus',    null, InputOption::VALUE_REQUIRED, 'Bank file status (default ALL).')
            ->addOption('filereference', null, InputOption::VALUE_REQUIRED, 'Restrict to a single fileReference.')
            ->addOption('syncdir',       null, InputOption::VALUE_REQUIRED, 'Local directory to drop downloaded files into.');
    }

    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank = (string)($this->opts['bank'] ?? '');
        $syncDir = (string)($this->opts['syncdir'] ?? '');
        if ($bank === '' || $syncDir === '') {
            $output->writeln('<error>--bank and --syncdir are required.</error>');
            return self::INVALID;
        }
        if (!is_dir($syncDir) && !@mkdir($syncDir, 0700, true) && !is_dir($syncDir)) {
            $output->writeln("<error>Cannot create --syncdir: $syncDir</error>");
            return self::FAILURE;
        }
        $status = (string)($this->opts['filestatus'] ?? '') ?: 'ALL';
        $type   = (string)($this->opts['filetype']   ?? '') ?: null;
        $idToken = (string)$this->opts['idtoken'];
        $apiKey  = (string)$this->opts['apikey'];

        try {
            $list = $this->filesApi()->listFiles($idToken, $apiKey, $bank, $status, $type);
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }

        $only = (string)($this->opts['filereference'] ?? '');
        $written = [];

        foreach (($list->getFiles() ?? []) as $file) {
            $ref = (string)$file->getFileReference();
            if ($only !== '' && $only !== $ref) {
                continue;
            }
            try {
                $resp = $this->filesApi()->downloadFile(
                    $idToken,
                    $apiKey,
                    $bank,
                    (string)$file->getFileType(),
                    $ref,
                );
            } catch (ApiException $e) {
                $output->writeln(sprintf('<error>download %s failed: %s</error>', $ref, $e->getMessage()));
                continue;
            }
            $decoded = base64_decode((string)$resp->getFileContents(), true);
            if ($decoded === false) {
                $output->writeln("<error>$ref: invalid base64 payload, skipping.</error>");
                continue;
            }
            $target = rtrim($syncDir, '/') . '/' . $this->safeFileName($ref, (string)$file->getFileType());
            if (file_put_contents($target, $decoded) === false) {
                $output->writeln("<error>Cannot write: $target</error>");
                continue;
            }
            $written[] = $target;
        }
        return $this->emit($input, $output, ['written' => $written, 'count' => count($written)]);
    }

    private function safeFileName(string $ref, string $type): string
    {
        $safe = preg_replace('/[^A-Za-z0-9._\-]+/', '_', $ref . '.' . $type);
        return (string)$safe;
    }
}
