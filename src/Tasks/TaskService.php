<?php

namespace BusinessXRay\Platform\Tasks;

defined('ABSPATH') || exit;

final class TaskService
{
    public const COMPLETE_ACTION = 'bxr_complete_task';

    public function maybe_complete_from_request(): ?string
    {
        if (! current_user_can('manage_options')) {
            return null;
        }

        $action = isset($_GET['bxr_action']) ? sanitize_key(wp_unslash($_GET['bxr_action'])) : '';
        if ($action !== self::COMPLETE_ACTION) {
            return null;
        }

        $task_id = isset($_GET['task_id']) ? absint($_GET['task_id']) : 0;
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

        if ($task_id <= 0 || ! wp_verify_nonce($nonce, self::COMPLETE_ACTION . '_' . $task_id)) {
            return __('Task could not be completed because the security check failed.', 'business-xray-platform');
        }

        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'bxr_tasks',
            [
                'status' => 'completed',
                'completed_at' => current_time('mysql'),
            ],
            ['id' => $task_id]
        );

        return __('Task marked as completed.', 'business-xray-platform');
    }

    public static function complete_url(int $task_id): string
    {
        return wp_nonce_url(
            admin_url('admin.php?page=bxr-tasks&bxr_action=' . self::COMPLETE_ACTION . '&task_id=' . $task_id),
            self::COMPLETE_ACTION . '_' . $task_id
        );
    }
}
