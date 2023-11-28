<?php

namespace app\core\database;

use app\core\Application;
use Symfony\Component\Console\Output\OutputInterface;

class Database
{
    public \PDO $pdo;
    public function __construct()
    {
        $dsn = "mysql:host=" . env("DB_HOST") . ";port=". env("DB_PORT") .";dbname=" . env("DB_DATABASE");

        $this->pdo = new \PDO(
            $dsn,
            env("DB_USERNAME"),
            env("DB_PASSWORD")
        );

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(OutputInterface $output)
    {
        $this->createMigrationsTable();

        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR.'/database/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);

        if (sizeof($toApplyMigrations) - 2 === 0) return $output->writeln("Nothing to migrate 😢");


        foreach ($toApplyMigrations as $migration) {
            if ($migration === "." || $migration === "..") continue;

            $migrationFilePath = Application::$ROOT_DIR."/database/migrations/".$migration;
            $migrationInstance = $this->includeMigrationFile($migrationFilePath);

            if ($migrationInstance !== null) {
                $this->executeMigration($migrationInstance, $output);
                $this->recordMigration($migration);

                $output->writeln("🚀 Applied migration: " . $migration);
            } else {
                $output->writeln("⚠️ Error loading migration: " . $migration);
            }
        }
    }

    protected function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    private function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function includeMigrationFile(string $filePath)
    {
        $migration = include $filePath;

        if (is_object($migration)) {
            return $migration;
        }

        return null;
    }

    private function executeMigration($migrationInstance, OutputInterface $output)
    {
        if (method_exists($migrationInstance, 'up')) {
            $migrationInstance->up();
        } else {
            $output->writeln("🚨 Missing 'up' method in migration.");
        }
    }

    private function recordMigration($migration)
    {
//        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
//        $statement->bindValue(':migration', $migration);
//        $statement->execute();
    }
}