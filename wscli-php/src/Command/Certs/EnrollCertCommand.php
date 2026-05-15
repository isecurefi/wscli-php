<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Certs;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\EnrollCertReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'certs:enroll', description: 'Enroll a bank certificate for the chosen bank.')]
final class EnrollCertCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('bank',     'b', InputOption::VALUE_REQUIRED, 'Bank slug (e.g. nordea).')
            ->addOption('pincode', null, InputOption::VALUE_REQUIRED, 'PIN code provided by the bank.')
            ->addOption('company', null, InputOption::VALUE_REQUIRED, 'Company name to enroll under.')
            ->addOption('wsuserid', null, InputOption::VALUE_REQUIRED, "WS user id (per bank's WebServices contract).");
    }

    protected function requiresAuthenticatedSession(): bool { return true; }
    protected function requiresAdminMode(): bool            { return true; }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bank     = (string)($this->opts['bank']     ?? '');
        $pin      = (string)($this->opts['pincode']  ?? '');
        $company  = (string)($this->opts['company']  ?? '');
        $wsUserId = (string)($this->opts['wsuserid'] ?? '');
        if ($bank === '' || $pin === '' || $wsUserId === '') {
            $output->writeln('<error>--bank, --pincode, --wsuserid are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->certsApi()->enrollCert(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $bank,
                new EnrollCertReq(['code' => $pin, 'company' => $company, 'wsUserId' => $wsUserId]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        return $this->emit($input, $output, $resp);
    }
}
