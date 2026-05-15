<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command\Session;

use IsecureFi\WsApi\Client\ApiException;
use IsecureFi\WsCli\Command\AbstractWsCliCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'session:logout', description: 'Revoke the stored IdToken and clear local session state.')]
final class LogoutCommand extends AbstractWsCliCommand
{
    protected function requiresAuthenticatedSession(): bool
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (string)($this->opts['email'] ?? '');
        $mode  = strtolower((string)($this->opts['mode'] ?? 'data'));
        if ($email === '') {
            $output->writeln('<error>--email is required.</error>');
            return self::INVALID;
        }
        try {
            $this->sessionApi()->logout(
                (string)$this->opts['idtoken'],
                (string)$this->opts['apikey'],
                $email,
                $mode,
            );
        } catch (ApiException $e) {
            // Still clear local state below — server may already consider us logged out.
            $this->handleApiException($e, $output);
        }

        if (! $input->getOption('no-save') && $this->settings && $this->settings->path()) {
            $this->settings->clear('idtoken');
            $this->settings->clear('expiresin');
            $this->settings->save();
        }
        return $this->emit($input, $output, ['logged_out' => true]);
    }
}
