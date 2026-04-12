<?php

/**
 * Router for `php -S` (Railway). Replaces Apache rewrite: non-file URIs → index.php.
 * Existing .php files under public/ (e.g. migrate_*.php) are executed directly.
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$full = realpath(__DIR__ . $path);
$base = realpath(__DIR__);

if ($path !== '/' && $path !== '' && $full && $base && str_starts_with($full, $base) && is_file($full) && str_ends_with($full, '.php')) {
    return false;
}

require __DIR__ . '/index.php';
