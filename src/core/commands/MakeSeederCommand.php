<?php

namespace app\core\commands;

use app\core\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeSeederCommand extends Command
{
    protected static $defaultName = 'make:seeder';

    protected function configure()
    {
        $this->setDescription('Create a seeder')
            ->addArgument(
                'seeder_name',
                InputArgument::REQUIRED,
                'Name of the seeder'
            )
            ->setHelp('This command allows you to create a migration seeder.');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seederName = $input->getArgument('seeder_name');


        $this->createMigration($seederName, $output);

        return Command::SUCCESS;
    }


    private function createMigration($seederName, $output)
    {
        $seederFileName = 'database/seeders/' . time() . "_" . $seederName .  '.php';

        if (!file_exists($seederFileName)) {
            file_put_contents($seederFileName, $this->getSeederStub());
            $output->writeln('😃 Seeder created successfully. ' . $seederFileName);
        } else {
            $output->writeln('😅 Seeder already exists.');
        }
    }

    private function getSeederStub(): string
    {

        return "<?php
namespace app\database\seeders;

use Faker\Factory;

return new class {
    public function up()
    {
        \$factory = Factory::create(config('fakerLocale'));

    }
};
";
    }
}
