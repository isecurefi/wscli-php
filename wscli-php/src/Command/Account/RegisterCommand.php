<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Account;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\RegisterReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'account:register', description: 'Register a new account (admin or data mode).')]
final class RegisterCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Account password (>=20 chars).')
            ->addOption('name',     null, InputOption::VALUE_REQUIRED, "Account owner's name.")
            ->addOption('company',  null, InputOption::VALUE_REQUIRED, "Account owner's company.")
            ->addOption('phone',    null, InputOption::VALUE_REQUIRED, "Account owner's phone (E.164, e.g. +358401234567).");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email   = (string)($this->opts['email']   ?? '');
        $mode    = (string)($this->opts['mode']    ?? '');
        $password = (string)($this->opts['password'] ?? '');
        $name    = (string)($this->opts['name']    ?? '');
        $company = (string)($this->opts['company'] ?? '');
        $phone   = (string)($this->opts['phone']   ?? '');
        $apiKey  = (string)($this->opts['apikey']  ?? '0');

        foreach (['email' => $email, 'mode' => $mode, 'password' => $password, 'phone' => $phone] as $field => $value) {
            if ($value === '') {
                $output->writeln("<error>--$field is required.</error>");
                return self::INVALID;
            }
        }
        if (strlen($password) < 20) {
            $output->writeln('<error>Password must be at least 20 characters.</error>');
            return self::INVALID;
        }

        $environment = $this->envResolver->normalizeForChallenge($this->opts['environment'] ?? null);

        try {
            $challengeStr = $this->challenge->fetch($email, $mode, useRegister: true);
            $encrypted    = $this->challenge->encryptPassword($password, $challengeStr, $environment);

            $resp = $this->accountApi()->register(
                $email,
                $mode,
                new RegisterReq([
                    'chResp'    => $challengeStr,
                    'encrypted' => $encrypted,
                    'apiKey'    => $apiKey,
                    'name'      => $name,
                    'company'   => $company,
                    'phone'     => $phone,
                ]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }

        $this->persist($input, [
            'email'   => $email,
            'mode'    => $mode,
            'apikey'  => $resp->getApiKey() ?: $apiKey,
            'phone'   => $phone,
            'name'    => $name,
            'company' => $company,
        ]);
        return $this->emit($input, $output, [
            'apikey'      => $resp->getApiKey(),
            'accessToken' => $resp->getAccessToken(),
            'responseCode'=> $resp->getResponseCode(),
        ]);
    }
}
