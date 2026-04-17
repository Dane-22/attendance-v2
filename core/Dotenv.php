<?php
/**
 * Simple Dotenv loader for PHP
 * Loads environment variables from .env file
 */
class Dotenv {
    private static $loaded = false;
    private static $variables = [];

    /**
     * Load .env file from the project root
     */
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }

        if ($path === null) {
            $path = __DIR__ . '/../.env';
        }

        if (!file_exists($path)) {
            return; // Silently skip if no .env file
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if ((strpos($value, '"') === 0 && substr($value, -1) === '"') ||
                    (strpos($value, "'") === 0 && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }

                self::$variables[$key] = $value;
                
                // Set as environment variable if not already set
                if (getenv($key) === false) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get a specific environment variable
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Check if running in development environment
     */
    public static function isDevelopment() {
        $env = self::get('APP_ENV', 'production');
        return in_array(strtolower($env), ['development', 'dev', 'local']);
    }

    /**
     * Check if running in production environment
     */
    public static function isProduction() {
        $env = self::get('APP_ENV', 'production');
        return in_array(strtolower($env), ['production', 'prod']);
    }
}
