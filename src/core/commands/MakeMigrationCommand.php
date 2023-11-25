<?php

namespace app\core\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    protected function configure()
    {
        $this->setDescription('Create a new migration')
            ->addArgument('migration_name', InputArgument::REQUIRED, 'Name of the migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrationName = $input->getArgument('migration_name');
        $this->createMigration($migrationName, $output);

        $output->writeln('Migration created successfully.');
        return Command::SUCCESS;
    }

    private function createMigration($migrationName, $output)
    {
        $migrationFileName = 'migrations/' . $migrationName . "_". time() . '.php';

        if (!file_exists($migrationFileName)) {
            file_put_contents($migrationFileName, $this->getMigrationStub());
        } else {
            $output->writeln('Migration already exists.');
        }
    }

    private function getMigrationStub()
    {
        return '<?php

';
    }
}
