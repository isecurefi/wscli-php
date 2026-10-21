<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Session;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Auth\SessionLogin;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Stand-alone MFA second leg. Most users will let `session:login` handle this
 * automatically; this command exists for scripted/CI flows that capture the
 * Session token separately and submit the verification code later.
 */
#[AsCommand(name: 'session:login-mfa', description: 'Complete admin MFA login by submitting an SMS or authenticator code.')]
final class LoginMfaCommand extends AbstractWsCliCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->addOption('code', null, InputOption::VALUE_REQUIRED, 'SMS or authenticator verification code.')
            ->addOption('challenge-name', null, InputOption::VALUE_REQUIRED, 'MFA challenge: SMS_MFA or SOFTWARE_TOKEN_MFA (defaults to SMS_MFA).')
            ->addOption('session', null, InputOption::VALUE_REQUIRED, 'MFA session token from the first login leg.');
    }

    protected function requiresAdminMode(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email   = (string) ($this->opts['email']   ?? '');
        $code    = (string) ($this->opts['code']    ?? '');
        $session = (string) ($this->opts['session'] ?? '');
        if ($email === '' || $code === '' || $session === '') {
            $output->writeln('<error>--email, --code and --session are required.</error>');
            return self::INVALID;
        }
        try {
            $resp = (new SessionLogin($this->config, $this->httpClient))->mfa(
                $email,
                $code,
                $session,
                (string) ($this->opts['challenge-name'] ?? 'SMS_MFA'),
            );
        } catch (ApiException $e) {
            return $this->handleApiException($e, $output);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::FAILURE;
        }
        $this->persist($input, [
            'email'     => $email,
            'mode'      => 'admin',
            'idtoken'   => ($resp['IdToken'] ?? null),
            'apikey'    => ($resp['ApiKey'] ?? null),
            'expiresin' => ($resp['ExpiresIn'] ?? null),
        ]);
        return $this->emit($input, $output, [
            'idtoken' => ($resp['IdToken'] ?? null),
            'apikey'  => ($resp['ApiKey'] ?? null),
        ]);
    }
}
