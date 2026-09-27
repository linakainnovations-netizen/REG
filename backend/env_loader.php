<?php
/**
 * Minimal .env loader (no dependencies).
 * Loads key=value pairs from the project-root .env into getenv()/$_ENV.
 * Safe to call multiple times. Missing .env = defaults apply.
 */

if (!function_exists('loadEnv')) {
    function loadEnv(string $dir): void
    {
        static $loaded = false;
        if ($loaded) return;
        $loaded = true;

        $file = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($file) || !is_readable($file)) return;

        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            $pos = strpos($line, '=');
            if ($pos === false) continue;
            $key = trim(substr($line, 0, $pos));
            $val = trim(substr($line, $pos + 1));
            // Strip surrounding quotes
            if (strlen($val) >= 2 && (($val[0] === '"' && $val[-1] === '"') || ($val[0] === "'" && $val[-1] === "'"))) {
                $val = substr($val, 1, -1);
            }
            if ($key !== '' && getenv($key) === false) {
                putenv("$key=$val");
                $_ENV[$key] = $val;
            }
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, string $default = ''): string
    {
        $v = getenv($key);
        if ($v === false && isset($_ENV[$key])) $v = $_ENV[$key];
        return ($v === false || $v === '') ? $default : (string)$v;
    }
}
