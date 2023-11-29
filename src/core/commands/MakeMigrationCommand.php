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

        return Command::SUCCESS;
    }

    private function createMigration($migrationName, $output)
    {
        $migrationFileName = 'database/migrations/' . $migrationName . "_". time() . '.php';

        if (!file_exists($migrationFileName)) {
            file_put_contents($migrationFileName, $this->getMigrationStub());
            $output->writeln('😃 Migration created successfully. ' . $migrationFileName);
        } else {
            $output->writeln('😅 Migration already exists.');
        }
    }

    private function getMigrationStub(): string
    {
        return '<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::create("", function (Columns $columns) {
        
        });
    }
};
';
    }
}
