<?php

namespace App\Config;

use PDO;

class Settings
{
    private static $settings = null;

    private static function load()
    {
        if (self::$settings === null) {
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->query("SELECT * FROM settings");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            self::$settings = [];
            foreach ($rows as $row) {
                self::$settings[$row['setting_key']] = $row['setting_value'];
            }
        }
    }

    public static function get($key, $default = null)
    {
        self::load();
        return self::$settings[$key] ?? $default;
    }

    public static function all()
    {
        self::load();
        return self::$settings;
    }
}
