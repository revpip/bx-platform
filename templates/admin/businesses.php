<?php
defined('ABSPATH') || exit;

global $wpdb;
$businesses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bxr_organisations ORDER BY created_at DESC LIMIT 50");
?>
<div class="wrap bxr-admin">
    <h1><?php esc_html_e('Businesses', 'business-xray-platform'); ?></h1>
    <p><?php esc_html_e('This will become the operating board for every business going through a Business X-Ray.', 'business-xray-platform'); ?></p>

    <div class="bxr-panel">
        <?php if (empty($businesses)) : ?>
            <p><?php esc_html_e('No businesses yet. Assessment and lead capture modules will populate this table.', 'business-xray-platform'); ?></p>
        <?php else : ?>
            <table class="widefat striped">
                <thead><tr><th><?php esc_html_e('Business', 'business-xray-platform'); ?></th><th><?php esc_html_e('Contact', 'business-xray-platform'); ?></th><th><?php esc_html_e('Status', 'business-xray-platform'); ?></th></tr></thead>
                <tbody>
                <?php foreach ($businesses as $business) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($business->name); ?></strong><br><?php echo esc_url($business->website); ?></td>
                        <td><?php echo esc_html($business->contact_name); ?><br><?php echo esc_html($business->contact_email); ?></td>
                        <td><?php echo esc_html(ucfirst($business->status)); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
