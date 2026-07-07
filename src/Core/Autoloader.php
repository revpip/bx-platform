<?php

namespace BusinessXRay\Platform\Core;

defined('ABSPATH') || exit;

final class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        $prefix = 'BusinessXRay\\Platform\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $relative = str_replace('\\', DIRECTORY_SEPARATOR, $relative);
        $file = BXR_PLATFORM_DIR . 'src/' . $relative . '.php';

        if (is_readable($file)) {
            require_once $file;
        }
    }
}
