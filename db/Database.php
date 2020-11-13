<?php

namespace app\core\db;

use app\core\Application;
use PDO;


class Database
{
    public $pdo;
    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function applyMigrations()
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_DIR . '/migrations');
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        $newMigrations = [];

        foreach ($toApplyMigrations as $migration) {
            if ($migration == '.' || $migration == '..') {
                continue;
            }
            require_once Application::$ROOT_DIR . '/migrations/' . $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME);
            $className = 'app\migrations\\' . $className;
            $instance = new $className();
            $this->log('applying migration');
            $instance->up();
            $this->log('applied migration');
            $newMigrations[] = $migration;
        }
        if (empty($newMigrations)) {
            $this->log('All migrations are processed');
        }
        $this->saveAppliedMigrations($newMigrations);
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )ENGINE=InnoDB;");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("
        SELECT migration FROM migrations;
        ");
        $statement->execute();
        $files = $statement->fetchAll(PDO::FETCH_COLUMN);
        return $files;
    }

    public function saveAppliedMigrations(array $appliedMigrations)
    {
        foreach ($appliedMigrations as $migration) {
            $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES(?)");
            $statement->execute([$migration]);
        }
    }

    protected function log($message)
    {
        echo date("Y/m/d H:i ").$message. PHP_EOL;
    }
}
