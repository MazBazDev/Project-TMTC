<?php

namespace app\core\commands;

use app\core\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setDescription('Create migrate the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Application::$app->db->applyMigrations($output);

        return Command::SUCCESS;
    }
}
