<?php

use BusinessXRay\Platform\Tasks\TaskService;

defined('ABSPATH') || exit;

$service = new TaskService();
$message = $service->maybe_complete_from_request();

global $wpdb;

$tasks = $wpdb->get_results("SELECT t.*, o.name AS organisation_name, o.contact_email FROM {$wpdb->prefix}bxr_tasks t LEFT JOIN {$wpdb->prefix}bxr_organisations o ON o.id = t.organisation_id ORDER BY FIELD(t.status, 'open', 'completed'), FIELD(t.priority, 'high', 'medium', 'low'), t.created_at DESC LIMIT 100");
?>
<div class="wrap bxr-admin">
    <h1><?php esc_html_e('Tasks', 'business-xray-platform'); ?></h1>
    <p><?php esc_html_e('Follow-up actions created by assessments and future workflows.', 'business-xray-platform'); ?></p>

    <?php if ($message) : ?>
        <div class="notice notice-success is-dismissible"><p><?php echo esc_html($message); ?></p></div>
    <?php endif; ?>

    <div class="bxr-panel">
        <?php if (empty($tasks)) : ?>
            <p><?php esc_html_e('No tasks yet. New Business X-Ray submissions will automatically create follow-up tasks.', 'business-xray-platform'); ?></p>
        <?php else : ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Task', 'business-xray-platform'); ?></th>
                        <th><?php esc_html_e('Business', 'business-xray-platform'); ?></th>
                        <th><?php esc_html_e('Priority', 'business-xray-platform'); ?></th>
                        <th><?php esc_html_e('Status', 'business-xray-platform'); ?></th>
                        <th><?php esc_html_e('Due', 'business-xray-platform'); ?></th>
                        <th><?php esc_html_e('Action', 'business-xray-platform'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tasks as $task) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($task->title); ?></strong></td>
                        <td><?php echo esc_html($task->organisation_name ?: '—'); ?><br><?php echo esc_html($task->contact_email ?: ''); ?></td>
                        <td><?php echo esc_html(ucfirst($task->priority)); ?></td>
                        <td><?php echo esc_html(ucfirst($task->status)); ?></td>
                        <td><?php echo esc_html($task->due_at ?: '—'); ?></td>
                        <td>
                            <?php if ($task->status !== 'completed') : ?>
                                <a class="button button-primary" href="<?php echo esc_url(TaskService::complete_url((int) $task->id)); ?>"><?php esc_html_e('Mark complete', 'business-xray-platform'); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($task->completed_at ?: __('Completed', 'business-xray-platform')); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
