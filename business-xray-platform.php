<?php
/**
 * Plugin Name: Business X-Ray Platform
 * Plugin URI: https://mcknightmarketing.co.uk/
 * Description: Business X-Ray assessment, scoring, reports and client intelligence platform.
 * Version: 0.1.0
 * Author: McKnight Intelligence
 * Text Domain: business-xray-platform
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

define('BXR_PLATFORM_VERSION', '0.1.0');
define('BXR_PLATFORM_FILE', __FILE__);
define('BXR_PLATFORM_DIR', plugin_dir_path(__FILE__));
define('BXR_PLATFORM_URL', plugin_dir_url(__FILE__));

require_once BXR_PLATFORM_DIR . 'src/Core/Autoloader.php';

BusinessXRay\Platform\Core\Autoloader::register();

register_activation_hook(__FILE__, ['BusinessXRay\\Platform\\Core\\Installer', 'activate']);
register_deactivation_hook(__FILE__, ['BusinessXRay\\Platform\\Core\\Installer', 'deactivate']);

add_action('plugins_loaded', static function (): void {
    BusinessXRay\Platform\Core\Plugin::instance()->boot();
});
