<?php

namespace app\core\commands;

use app\core\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageLinkCommand extends Command
{
    protected static $defaultName = 'storage:link';

    protected function configure()
    {
        $this->setDescription('Create a symbolic link from ./src/storage to ./src/public/');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root = Application::$ROOT_DIR;

        $srcPath = $root . '/storage/';
        $linkPath = $root . config("storageDir");

        if (is_link($linkPath)) {
            $output->writeln('<error>Symbolic link already exists. Aborting...</error>');
            return Command::FAILURE;
        }

        symlink($srcPath, $linkPath);

        $output->writeln('<info>Symbolic link created successfully.</info>');
        return Command::SUCCESS;
    }
}
