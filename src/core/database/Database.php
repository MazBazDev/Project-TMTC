<?php

namespace app\core\database;

use app\core\Application;
use Symfony\Component\Console\Output\OutputInterface;

class Database
{
    public \PDO $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=" . env("DB_HOST") . ";port=" . env("DB_PORT") . ";dbname=" . env("DB_DATABASE");

        $this->pdo = new \PDO(
            $dsn,
            env("DB_USERNAME"),
            env("DB_PASSWORD")
        );

        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(OutputInterface $output)
    {
        $this->applyMigrationsOrSeeders('migrations', 'Applied migration', 'migrations', $output);
    }

    public function applySeed(OutputInterface $output)
    {
        $this->applyMigrationsOrSeeders('seeders', 'Applied seeder', 'seeders', $output);
    }

    protected function applyMigrationsOrSeeders($tableName, $successMessage, $directory, OutputInterface $output)
    {
        $this->createMigrationsTable($tableName);

        $appliedMigrations = $this->getAppliedMigrations($tableName);

        $files = scandir(Application::$ROOT_DIR . '/database/' . $directory);

        foreach ($files as $file) {
            if ($file === "." || $file === "..") continue;

            $filePath = Application::$ROOT_DIR . "/database/$directory/$file";
            $instance = $this->includeMigrationFile($filePath);

            if ($instance !== null && !in_array($file, $appliedMigrations)) {

                try {
                    $this->executeMigration($instance, $output);
                    $this->recordMigration($tableName, $file);

                    $output->writeln("🚀 $successMessage: $file");
                } catch (\Exception $e) {
                    $output->writeln("⚠️ Error loading $directory '$file': " . $e->getMessage());
                }

            } elseif (in_array($file, $appliedMigrations)) {
                $output->writeln("⚠️ $directory '$file' already applied");
            } else {
                $output->writeln("⚠️ Error loading $directory: $file");
            }
        }
    }

    protected function createMigrationsTable($tableName)
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS $tableName (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )  ENGINE=INNODB;");
    }

    private function getAppliedMigrations($tableName)
    {
        $statement = $this->pdo->prepare("SELECT migration FROM $tableName");

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

    private function executeMigration($instance, OutputInterface $output)
    {
        if (method_exists($instance, 'up')) {
            $instance->up();
        } else {
            throw new \Exception("Missing 'up' method in migration or seeder.");
        }
    }

    private function recordMigration($tableName, $migration)
    {
        $statement = $this->pdo->prepare("INSERT INTO $tableName (migration) VALUES (:migration)");
        $statement->bindValue(':migration', $migration);
        $statement->execute();
    }
}
