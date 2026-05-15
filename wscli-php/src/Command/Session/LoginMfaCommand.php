<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Session;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsApi\Client\Model\LoginMFAReq;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stand-alone MFA second leg. Most users will let `session:login` handle this
 * automatically; this command exists for scripted/CI flows that capture the
 * Session token separately and submit the SMS code later.
 */
#[AsCommand(name: 'session:login-mfa', description: 'Complete admin MFA login by submitting an SMS code.')]
final class LoginMfaCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('code',    null, InputOption::VALUE_REQUIRED, 'SMS MFA code.')
            ->addOption('session', null, InputOption::VALUE_REQUIRED, 'MFA session token from the first login leg.');
    }

    protected function requiresAdminMode(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email   = (string)($this->opts['email']   ?? '');
        $code    = (string)($this->opts['code']    ?? '');
        $session = (string)($this->opts['session'] ?? '');
        if ($email === '' || $code === '' || $session === '') {
            $output->writeln('<error>--email, --code and --session are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = $this->sessionApi()->loginMFA(
                $email,
                'admin',
                new LoginMFAReq(['code' => $code, 'session' => $session]),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        }
        $this->persist($input, [
            'email'     => $email,
            'mode'      => 'admin',
            'idtoken'   => $resp->getIdToken(),
            'apikey'    => $resp->getApiKey(),
            'expiresin' => $resp->getExpiresIn(),
        ]);
        return $this->emit($input, $output, [
            'idtoken' => $resp->getIdToken(),
            'apikey'  => $resp->getApiKey(),
        ]);
    }
}
