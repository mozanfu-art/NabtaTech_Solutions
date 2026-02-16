<?php

declare(strict_types=1);

function env(string $key, ?string $default = null): ?string
{
    static $loaded = false;

    if (!$loaded) {
        $envPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
        if (is_file($envPath)) {
            $vars = parse_ini_file($envPath, false, INI_SCANNER_TYPED);
            if (is_array($vars)) {
                foreach ($vars as $name => $value) {
                    if (getenv($name) === false) {
                        putenv($name . '=' . $value);
                    }
                }
            }
        }
        $loaded = true;
    }

    $value = getenv($key);
    if ($value === false) {
        return $default;
    }

    return (string) $value;
}
