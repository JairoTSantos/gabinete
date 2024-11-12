<?php

namespace Jairosantos\GabineteDigital\Core;
use Jairosantos\GabineteDigital\Core\Logger;


use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private $connection;
    private $logger;


    public function __construct() {
        $this->logger = new Logger();
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname",
                $username,
                $password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->novoLog('db_error', $e->getMessage());
            header('Location: ?secao=error');
            
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
