<?php

declare(strict_types=1);

namespace IsecureFi\WsCli\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'rollback', description: 'Roll the phar back to the previous version.')]
final class RollbackCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (\Phar::running() === '') {
            $output->writeln('<comment>rollback is only meaningful when running from a phar.</comment>');
            return self::INVALID;
        }
        $updater = new Updater(null, true);
        if (!$updater->rollback()) {
            $output->writeln('<error>No backup to roll back to.</error>');
            return self::FAILURE;
        }
        $output->writeln('<info>Rolled back successfully.</info>');
        return self::SUCCESS;
    }
}
