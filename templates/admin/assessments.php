<?php
defined('ABSPATH') || exit;

global $wpdb;
$assessments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bxr_assessments ORDER BY created_at DESC LIMIT 50");
?>
<div class="wrap bxr-admin">
    <h1><?php esc_html_e('Assessments', 'business-xray-platform'); ?></h1>
    <p><?php esc_html_e('Submitted Business X-Ray assessments will appear here with scores and report links.', 'business-xray-platform'); ?></p>

    <div class="bxr-panel">
        <?php if (empty($assessments)) : ?>
            <p><?php esc_html_e('No assessments yet.', 'business-xray-platform'); ?></p>
        <?php else : ?>
            <table class="widefat striped">
                <thead><tr><th><?php esc_html_e('Name', 'business-xray-platform'); ?></th><th><?php esc_html_e('Website', 'business-xray-platform'); ?></th><th><?php esc_html_e('Score', 'business-xray-platform'); ?></th><th><?php esc_html_e('Date', 'business-xray-platform'); ?></th></tr></thead>
                <tbody>
                <?php foreach ($assessments as $assessment) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($assessment->name); ?></strong><br><?php echo esc_html($assessment->email); ?></td>
                        <td><?php echo esc_url($assessment->website); ?></td>
                        <td><strong><?php echo esc_html((string) $assessment->overall_score); ?></strong></td>
                        <td><?php echo esc_html($assessment->created_at); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
