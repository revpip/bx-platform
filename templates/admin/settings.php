<?php

use BusinessXRay\Platform\Settings\Settings;

defined('ABSPATH') || exit;
$options = Settings::get();
?>
<div class="wrap bxr-admin">
    <h1><?php esc_html_e('Business X-Ray Settings', 'business-xray-platform'); ?></h1>

    <form method="post" action="options.php" class="bxr-panel bxr-settings-form">
        <?php settings_fields('bxr_platform_settings'); ?>

        <label>
            <span><?php esc_html_e('Lead notification email', 'business-xray-platform'); ?></span>
            <input type="email" name="<?php echo esc_attr(Settings::OPTION); ?>[lead_email]" value="<?php echo esc_attr($options['lead_email']); ?>">
        </label>

        <label>
            <span><?php esc_html_e('Booking URL', 'business-xray-platform'); ?></span>
            <input type="url" name="<?php echo esc_attr(Settings::OPTION); ?>[booking_url]" value="<?php echo esc_attr($options['booking_url']); ?>">
        </label>

        <label>
            <span><?php esc_html_e('Public headline', 'business-xray-platform'); ?></span>
            <input type="text" name="<?php echo esc_attr(Settings::OPTION); ?>[headline]" value="<?php echo esc_attr($options['headline']); ?>">
        </label>

        <label>
            <span><?php esc_html_e('Accent colour', 'business-xray-platform'); ?></span>
            <input type="text" name="<?php echo esc_attr(Settings::OPTION); ?>[accent_colour]" value="<?php echo esc_attr($options['accent_colour']); ?>">
        </label>

        <label>
            <span><?php esc_html_e('Confidentiality note', 'business-xray-platform'); ?></span>
            <textarea name="<?php echo esc_attr(Settings::OPTION); ?>[confidentiality_note]" rows="4"><?php echo esc_textarea($options['confidentiality_note']); ?></textarea>
        </label>

        <?php submit_button(__('Save Settings', 'business-xray-platform')); ?>
    </form>
</div>
