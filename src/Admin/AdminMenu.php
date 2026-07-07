<?php

namespace BusinessXRay\Platform\Admin;

defined('ABSPATH') || exit;

final class AdminMenu
{
    public function register(): void
    {
        add_action('admin_menu', [$this, 'add_pages']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_pages(): void
    {
        add_menu_page(
            __('Business X-Ray', 'business-xray-platform'),
            __('Business X-Ray', 'business-xray-platform'),
            'manage_options',
            'bxr-dashboard',
            [$this, 'render_dashboard'],
            'dashicons-chart-area',
            26
        );

        add_submenu_page('bxr-dashboard', __('Dashboard', 'business-xray-platform'), __('Dashboard', 'business-xray-platform'), 'manage_options', 'bxr-dashboard', [$this, 'render_dashboard']);
        add_submenu_page('bxr-dashboard', __('Businesses', 'business-xray-platform'), __('Businesses', 'business-xray-platform'), 'manage_options', 'bxr-businesses', [$this, 'render_businesses']);
        add_submenu_page('bxr-dashboard', __('Assessments', 'business-xray-platform'), __('Assessments', 'business-xray-platform'), 'manage_options', 'bxr-assessments', [$this, 'render_assessments']);
        add_submenu_page('bxr-dashboard', __('Settings', 'business-xray-platform'), __('Settings', 'business-xray-platform'), 'manage_options', 'bxr-settings', [$this, 'render_settings']);
    }

    public function enqueue_assets(string $hook): void
    {
        if (strpos($hook, 'bxr-') === false) {
            return;
        }

        wp_enqueue_style('bxr-admin', BXR_PLATFORM_URL . 'assets/css/admin.css', [], BXR_PLATFORM_VERSION);
    }

    public function render_dashboard(): void
    {
        include BXR_PLATFORM_DIR . 'templates/admin/dashboard.php';
    }

    public function render_businesses(): void
    {
        include BXR_PLATFORM_DIR . 'templates/admin/businesses.php';
    }

    public function render_assessments(): void
    {
        include BXR_PLATFORM_DIR . 'templates/admin/assessments.php';
    }

    public function render_settings(): void
    {
        include BXR_PLATFORM_DIR . 'templates/admin/settings.php';
    }
}
