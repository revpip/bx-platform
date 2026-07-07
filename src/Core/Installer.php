<?php

namespace BusinessXRay\Platform\Core;

defined('ABSPATH') || exit;

final class Installer
{
    public static function activate(): void
    {
        self::create_tables();
        update_option('bxr_platform_version', BXR_PLATFORM_VERSION);
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }

    private static function create_tables(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $organisations = $wpdb->prefix . 'bxr_organisations';
        $assessments = $wpdb->prefix . 'bxr_assessments';
        $tasks = $wpdb->prefix . 'bxr_tasks';
        $activity = $wpdb->prefix . 'bxr_activity_log';

        dbDelta("CREATE TABLE {$organisations} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(190) NOT NULL,
            website VARCHAR(255) NULL,
            industry VARCHAR(190) NULL,
            contact_name VARCHAR(190) NULL,
            contact_email VARCHAR(190) NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'lead',
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY contact_email (contact_email)
        ) {$charset};");

        dbDelta("CREATE TABLE {$assessments} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            organisation_id BIGINT UNSIGNED NULL,
            name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NULL,
            website VARCHAR(255) NULL,
            scores LONGTEXT NULL,
            answers LONGTEXT NULL,
            overall_score INT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY organisation_id (organisation_id),
            KEY overall_score (overall_score)
        ) {$charset};");

        dbDelta("CREATE TABLE {$tasks} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            organisation_id BIGINT UNSIGNED NULL,
            title VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'open',
            priority VARCHAR(50) NOT NULL DEFAULT 'medium',
            due_at DATETIME NULL,
            created_at DATETIME NOT NULL,
            completed_at DATETIME NULL,
            PRIMARY KEY  (id),
            KEY organisation_id (organisation_id),
            KEY status (status)
        ) {$charset};");

        dbDelta("CREATE TABLE {$activity} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            organisation_id BIGINT UNSIGNED NULL,
            type VARCHAR(100) NOT NULL,
            summary TEXT NOT NULL,
            meta LONGTEXT NULL,
            created_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY organisation_id (organisation_id),
            KEY type (type)
        ) {$charset};");
    }
}
