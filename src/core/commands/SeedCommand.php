<?php

namespace app\core\commands;

use app\core\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command
{
    protected static $defaultName = 'seed';

    protected function configure()
    {
        $this->setDescription('Seed the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Application::$app->db->applySeed($output);

        return Command::SUCCESS;
    }
}