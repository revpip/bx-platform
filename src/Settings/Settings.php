<?php

namespace BusinessXRay\Platform\Settings;

defined('ABSPATH') || exit;

final class Settings
{
    public const OPTION = 'bxr_platform_settings';

    public function register(): void
    {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings(): void
    {
        register_setting('bxr_platform_settings', self::OPTION, [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize'],
            'default' => self::defaults(),
        ]);
    }

    public static function defaults(): array
    {
        return [
            'lead_email' => get_option('admin_email'),
            'booking_url' => '',
            'headline' => 'Every business has blind spots. Business X-Ray reveals them.',
            'accent_colour' => '#d4af37',
            'confidentiality_note' => 'All Business X-Ray conversations are confidential. NDA available on request.',
        ];
    }

    public static function get(): array
    {
        $settings = get_option(self::OPTION, []);
        return wp_parse_args(is_array($settings) ? $settings : [], self::defaults());
    }

    public static function sanitize($value): array
    {
        $value = is_array($value) ? $value : [];

        return [
            'lead_email' => sanitize_email($value['lead_email'] ?? get_option('admin_email')),
            'booking_url' => esc_url_raw($value['booking_url'] ?? ''),
            'headline' => sanitize_text_field($value['headline'] ?? ''),
            'accent_colour' => sanitize_hex_color($value['accent_colour'] ?? '#d4af37') ?: '#d4af37',
            'confidentiality_note' => sanitize_textarea_field($value['confidentiality_note'] ?? ''),
        ];
    }
}
