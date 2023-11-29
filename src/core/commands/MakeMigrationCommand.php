<?php

namespace app\core\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';

    protected function configure()
    {
        $this->setDescription('Create or edit a migration')
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Specify the migration type: create or edit'
            )
            ->addOption(
                'table',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify the name of the table'
            )
            ->addArgument(
                'migration_name',
                InputArgument::REQUIRED,
                'Name of the migration'
            )
            ->setHelp('This command allows you to create or edit a migration.');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrationName = $input->getArgument('migration_name');
        $migrationType = $input->getOption('type') ?? "create";
        $tableName = $input->getOption('table') ?? "";

        if (!in_array($migrationType, ['create', 'edit'])) {
            throw new \InvalidArgumentException('Invalid migration type. Use "create" or "edit".');
        }

        $this->createMigration($migrationType, $tableName, $migrationName, $output);

        return Command::SUCCESS;
    }


    private function createMigration($type, $tableName, $migrationName, $output)
    {
        $migrationFileName = 'database/migrations/' . time() . "_" . $migrationName .  '.php';

        if (!file_exists($migrationFileName)) {
            file_put_contents($migrationFileName, $this->getMigrationStub($type, $tableName));
            $output->writeln('😃 Migration created successfully. ' . $migrationFileName);
        } else {
            $output->writeln('😅 Migration already exists.');
        }
    }

    private function getMigrationStub($type, $tableName): string
    {
        return "<?php
namespace app\database\migrations;

use app\core\database\Schema;
use app\core\database\Columns;

return new class {
    public function up()
    {
        Schema::$type(\"$tableName\", function (Columns \$columns) {
        
        });
    }
};
";
    }
}
