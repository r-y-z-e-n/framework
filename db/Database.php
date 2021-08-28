<?php

namespace ryzen\framework\db;

use ryzen\framework\Application;

/**
 * @author razoo.choudhary@gmail.com
 * Class Database
 * @package ryzen\framework
 */
class Database
{

    public \PDO $pdo;

    /**
     * Database constructor.
     */

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {

        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        $files = scandir(Application::$ROOT_DIR . '/migrations');

        $leftOverMigrations = array_diff($files, $appliedMigrations);

        foreach ($leftOverMigrations as $migration) {

            if ($migration === '.' || $migration === '..') {

                continue;
            }

            require_once Application::$ROOT_DIR . '/migrations/' . $migration;

            $className = pathinfo($migration, PATHINFO_FILENAME);
            $instance = new $className();

            $this->log("Running ry-f Migration $migration");
            $instance->up();
            $this->log("Migrate $migration Success");

            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {

            $this->saveMigrations($newMigrations);

        } else {

            $this->log("Migrations are applied");
        }
    }

    public function createMigrationsTable()
    {

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
    
    id INT AUTO_INCREMENT PRIMARY KEY,migration VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )
    
    ENGINE=INNODB;");

    }

    public function getAppliedMigrations()
    {

        $this->pdo->exec("DELETE FROM migrations");

        $statement = $this->pdo->prepare("SELECT migration FROM migrations");

        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {

        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES 
                                          $str
                                          ");
        $statement->execute();
    }

    public function prepare($sql)
    {

        return $this->pdo->prepare($sql);
    }

    protected function log($message)
    {

        echo '[' . date('Y-m-d H:i:S') . '] - ' . $message . PHP_EOL;
    }
}