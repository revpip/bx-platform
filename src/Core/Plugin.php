<?php

namespace BusinessXRay\Platform\Core;

use BusinessXRay\Platform\Admin\AdminMenu;
use BusinessXRay\Platform\Frontend\Shortcodes;
use BusinessXRay\Platform\Settings\Settings;

defined('ABSPATH') || exit;

final class Plugin
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    public function boot(): void
    {
        add_action('init', [$this, 'load_textdomain']);

        (new Shortcodes())->register();

        if (is_admin()) {
            (new AdminMenu())->register();
            (new Settings())->register();
        }
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'business-xray-platform',
            false,
            dirname(plugin_basename(BXR_PLATFORM_FILE)) . '/languages'
        );
    }
}
