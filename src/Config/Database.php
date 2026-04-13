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
        $this->db_name = 'supplier_saas';
        $fromUrl = false;
        $mysqlUrl = getenv('MYSQL_URL');
        if ($mysqlUrl !== false && $mysqlUrl !== '') {
            $fromUrl = $this->applyMysqlUrl($mysqlUrl);
        }
        if (!$fromUrl) {
            $databaseUrl = getenv('DATABASE_URL');
            if ($databaseUrl !== false && $databaseUrl !== '' && str_starts_with($databaseUrl, 'mysql://')) {
                $fromUrl = $this->applyMysqlUrl($databaseUrl);
            }
        }
        if (!$fromUrl) {
            // DB_*; Railway also exposes MYSQLHOST / MYSQLPORT / ... (no underscore after MYSQL)
            $this->host = $this->firstEnv(['DB_HOST', 'MYSQL_HOST', 'MYSQLHOST'], 'localhost');
            $this->port = $this->firstEnv(['DB_PORT', 'MYSQL_PORT', 'MYSQLPORT'], '3306');
            $this->db_name = $this->firstEnv(['DB_NAME', 'MYSQL_DATABASE', 'MYSQLDATABASE'], 'supplier_saas');
            $this->username = $this->firstEnv(['DB_USER', 'MYSQL_USER', 'MYSQLUSER'], 'root');
            $this->password = $this->firstEnv(['DB_PASSWORD', 'MYSQL_PASSWORD', 'MYSQLPASSWORD'], '');
        }
    }

    /**
     * Railway / platforms often set MYSQL_URL=mysql://user:pass@host:port/dbname
     */
    private function applyMysqlUrl(string $url): bool
    {
        $parts = parse_url($url);
        if ($parts === false || ($parts['scheme'] ?? '') !== 'mysql') {
            return false;
        }
        $this->host = $parts['host'] ?? 'localhost';
        $this->port = isset($parts['port']) ? (string) (int) $parts['port'] : '3306';
        $this->username = isset($parts['user']) ? rawurldecode($parts['user']) : 'root';
        $this->password = isset($parts['pass']) ? rawurldecode($parts['pass']) : '';
        $path = $parts['path'] ?? '';
        $db = ltrim((string) $path, '/');
        if ($db !== '') {
            $this->db_name = $db;
        }
        return true;
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
            // Linux PDO + host "localhost" uses a Unix socket → 2002 No such file in Docker/cloud
            $host = $this->host;
            if ($host === 'localhost' || $host === '::1') {
                $host = '127.0.0.1';
            }
            $dsn = 'mysql:host=' . $host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8mb4';
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec('SET NAMES utf8mb4');
        } catch (PDOException $exception) {
            error_log('Database connection failed: ' . $exception->getMessage());
        }

        return $this->conn;
    }
}
