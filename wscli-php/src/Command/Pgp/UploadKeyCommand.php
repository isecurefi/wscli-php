<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Pgp;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\UploadKeyReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'pgp:upload', description: 'Upload an armored PGP public key.')]
final class UploadKeyCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('pgpkeycontents', null, InputOption::VALUE_REQUIRED, 'Armored PGP public key contents.')
            ->addOption('pgpkeyfile',     null, InputOption::VALUE_REQUIRED, 'Path to an armored PGP public key file.')
            ->addOption('pgpkeypurpose',  null, InputOption::VALUE_REQUIRED, 'authorization | export (default: authorization).');
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
        $contents = (string)($this->opts['pgpkeycontents'] ?? '');
        $file     = (string)($this->opts['pgpkeyfile']     ?? '');
        if ($contents === '' && $file === '') {
            $output->writeln('<error>One of --pgpkeycontents or --pgpkeyfile is required.</error>');
            return self::INVALID;
        }
        if ($file !== '') {
            if (!is_file($file)) {
                $output->writeln("<error>Cannot read --pgpkeyfile: $file</error>");
                return self::FAILURE;
            }
            $contents = (string)file_get_contents($file);
        }
        $purpose = (string)($this->opts['pgpkeypurpose'] ?? 'authorization');

        try {
            $resp = $this->pgpApi()->uploadKey(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                new UploadKeyReq(['pgpKey' => $contents, 'pgpKeyPurpose' => $purpose]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
