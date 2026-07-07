<?php
/** @var wpdb $wpdb */
defined('ABSPATH') || exit;

global $wpdb;
$org_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bxr_organisations");
$assessment_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bxr_assessments");
$task_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bxr_tasks WHERE status = 'open'");
$avg_score = (int) $wpdb->get_var("SELECT AVG(overall_score) FROM {$wpdb->prefix}bxr_assessments");
?>
<div class="wrap bxr-admin">
    <div class="bxr-hero-panel">
        <p class="bxr-kicker"><?php esc_html_e('Business X-Ray Platform', 'business-xray-platform'); ?></p>
        <h1><?php esc_html_e('Good morning. Here is what needs attention.', 'business-xray-platform'); ?></h1>
        <p><?php esc_html_e('Your Business X-Ray dashboard brings leads, assessments, reports and action plans into one operating view.', 'business-xray-platform'); ?></p>
    </div>

    <div class="bxr-grid bxr-grid-4">
        <div class="bxr-card"><span><?php esc_html_e('Businesses', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $org_count); ?></strong></div>
        <div class="bxr-card"><span><?php esc_html_e('Assessments', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $assessment_count); ?></strong></div>
        <div class="bxr-card"><span><?php esc_html_e('Open Tasks', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $task_count); ?></strong></div>
        <div class="bxr-card"><span><?php esc_html_e('Average Score', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $avg_score); ?></strong></div>
    </div>

    <div class="bxr-panel">
        <h2><?php esc_html_e('Sprint 1 Foundation', 'business-xray-platform'); ?></h2>
        <p><?php esc_html_e('This repository now contains the production plugin bootstrap, installer, settings, admin shell and first database tables.', 'business-xray-platform'); ?></p>
    </div>
</div>
