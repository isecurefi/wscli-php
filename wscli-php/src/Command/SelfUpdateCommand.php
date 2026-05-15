<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command;

use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'self-update', description: 'Update the wscli phar in place from the latest GitHub release.')]
final class SelfUpdateCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('pubkey', null, InputOption::VALUE_REQUIRED, 'Path to phar verification public key.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (\Phar::running() === '') {
            $output->writeln('<comment>self-update is only meaningful when running from a phar.</comment>');
            return self::INVALID;
        }
        $updater = new Updater(null, true);
        $updater->setStrategyObject(
            (new GithubStrategy())
                ->setPackageName('isecurefi/wscli-php')
                ->setPharName('wscli.phar')
                ->setCurrentLocalVersion(\IsecureFi\WsCli\Application::VERSION),
        );
        if (!$updater->update()) {
            $output->writeln('<info>Already at the latest release.</info>');
            return self::SUCCESS;
        }
        $output->writeln(sprintf('<info>Updated from %s -> %s</info>', $updater->getOldVersion(), $updater->getNewVersion()));
        return self::SUCCESS;
    }
}
