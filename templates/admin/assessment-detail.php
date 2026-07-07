<?php

defined('ABSPATH') || exit;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have permission to view this assessment.', 'business-xray-platform'));
}

global $wpdb;

$assessment_id = isset($_GET['assessment_id']) ? absint($_GET['assessment_id']) : 0;
$assessment = null;
$organisation = null;
$activity = [];

if ($assessment_id > 0) {
    $assessment = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bxr_assessments WHERE id = %d", $assessment_id));
}

if ($assessment && ! empty($assessment->organisation_id)) {
    $organisation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bxr_organisations WHERE id = %d", (int) $assessment->organisation_id));
    $activity = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bxr_activity_log WHERE organisation_id = %d ORDER BY created_at DESC LIMIT 10", (int) $assessment->organisation_id));
}

$scores = $assessment ? json_decode((string) $assessment->scores, true) : [];
$answers = $assessment ? json_decode((string) $assessment->answers, true) : [];
$answer_values = is_array($answers['answers'] ?? null) ? $answers['answers'] : [];
$frustration = isset($answers['biggest_frustration']) ? (string) $answers['biggest_frustration'] : '';
?>
<div class="wrap bxr-admin">
    <p><a href="<?php echo esc_url(admin_url('admin.php?page=bxr-assessments')); ?>">← <?php esc_html_e('Back to assessments', 'business-xray-platform'); ?></a></p>

    <?php if (! $assessment) : ?>
        <div class="bxr-panel">
            <h1><?php esc_html_e('Assessment not found', 'business-xray-platform'); ?></h1>
            <p><?php esc_html_e('The requested Business X-Ray assessment could not be found.', 'business-xray-platform'); ?></p>
        </div>
    <?php else : ?>
        <div class="bxr-hero-panel">
            <p class="bxr-kicker"><?php esc_html_e('Business X-Ray Detail', 'business-xray-platform'); ?></p>
            <h1><?php echo esc_html($assessment->name); ?></h1>
            <p><?php echo esc_html($assessment->email); ?> · <?php echo esc_url($assessment->website); ?></p>
        </div>

        <div class="bxr-grid bxr-grid-4">
            <div class="bxr-card"><span><?php esc_html_e('Overall Score', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $assessment->overall_score); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Band', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) ($scores['band'] ?? '—')); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Industry', 'business-xray-platform'); ?></span><strong><?php echo esc_html($organisation->industry ?? '—'); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Status', 'business-xray-platform'); ?></span><strong><?php echo esc_html(ucfirst($organisation->status ?? 'assessment')); ?></strong></div>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Recommended Next Action', 'business-xray-platform'); ?></h2>
            <p><?php echo esc_html((string) ($scores['recommendation'] ?? __('Review this assessment and book a follow-up conversation.', 'business-xray-platform'))); ?></p>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Biggest Frustration', 'business-xray-platform'); ?></h2>
            <p><?php echo $frustration !== '' ? esc_html($frustration) : esc_html__('No frustration entered.', 'business-xray-platform'); ?></p>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Pillar Scores', 'business-xray-platform'); ?></h2>
            <?php if (! empty($scores['pillars']) && is_array($scores['pillars'])) : ?>
                <div class="bxr-score-list">
                    <?php foreach ($scores['pillars'] as $pillar => $score) : ?>
                        <div class="bxr-score-row">
                            <span><?php echo esc_html((string) $pillar); ?></span>
                            <strong><?php echo esc_html((string) $score); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e('No pillar scores stored.', 'business-xray-platform'); ?></p>
            <?php endif; ?>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Raw Answer Scores', 'business-xray-platform'); ?></h2>
            <?php if (! empty($answer_values)) : ?>
                <table class="widefat striped">
                    <thead><tr><th><?php esc_html_e('Question Key', 'business-xray-platform'); ?></th><th><?php esc_html_e('Score', 'business-xray-platform'); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($answer_values as $key => $value) : ?>
                        <tr><td><?php echo esc_html((string) $key); ?></td><td><strong><?php echo esc_html((string) $value); ?></strong></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No answers stored.', 'business-xray-platform'); ?></p>
            <?php endif; ?>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Recent Activity', 'business-xray-platform'); ?></h2>
            <?php if (! empty($activity)) : ?>
                <ul class="bxr-activity-list">
                    <?php foreach ($activity as $item) : ?>
                        <li><strong><?php echo esc_html($item->created_at); ?></strong> — <?php echo esc_html($item->summary); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p><?php esc_html_e('No activity yet.', 'business-xray-platform'); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
