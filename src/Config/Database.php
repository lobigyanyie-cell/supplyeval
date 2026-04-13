<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        // DB_*; Railway MySQL template uses MYSQLHOST / MYSQLPORT / ... (no underscore after MYSQL)
        $this->host = $this->firstEnv(['DB_HOST', 'MYSQL_HOST', 'MYSQLHOST'], 'localhost');
        $this->port = $this->firstEnv(['DB_PORT', 'MYSQL_PORT', 'MYSQLPORT'], '3306');
        $this->db_name = $this->firstEnv(['DB_NAME', 'MYSQL_DATABASE', 'MYSQLDATABASE'], 'supplier_saas');
        $this->username = $this->firstEnv(['DB_USER', 'MYSQL_USER', 'MYSQLUSER'], 'root');
        $pw = $this->firstEnv(['DB_PASSWORD', 'MYSQL_PASSWORD', 'MYSQLPASSWORD'], '');
        $this->password = $pw;
    }

    /**
     * @param list<string> $keys
     */
    private function firstEnv(array $keys, string $default): string
    {
        foreach ($keys as $key) {
            $v = getenv($key);
            if ($v !== false && $v !== '') {
                return $v;
            }
        }
        return $default;
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
