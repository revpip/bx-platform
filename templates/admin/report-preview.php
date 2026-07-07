<?php

use BusinessXRay\Platform\Reports\ReportService;

defined('ABSPATH') || exit;

if (! current_user_can('manage_options')) {
    wp_die(esc_html__('You do not have permission to view this report.', 'business-xray-platform'));
}

$assessment_id = isset($_GET['assessment_id']) ? absint($_GET['assessment_id']) : 0;
$service = new ReportService();
$assessment = $service->load_assessment($assessment_id);
$scores = $assessment ? $service->decode_scores($assessment) : [];
$answers = $assessment ? $service->decode_answers($assessment) : [];
$actions = $service->priority_actions($scores);
$frustration = isset($answers['biggest_frustration']) ? (string) $answers['biggest_frustration'] : '';
?>
<div class="wrap bxr-admin">
    <p><a href="<?php echo esc_url(admin_url('admin.php?page=bxr-assessment-detail&assessment_id=' . $assessment_id)); ?>">← <?php esc_html_e('Back to assessment', 'business-xray-platform'); ?></a></p>

    <?php if (! $assessment) : ?>
        <div class="bxr-panel">
            <h1><?php esc_html_e('Report not found', 'business-xray-platform'); ?></h1>
            <p><?php esc_html_e('The requested assessment could not be found.', 'business-xray-platform'); ?></p>
        </div>
    <?php else : ?>
        <div class="bxr-hero-panel">
            <p class="bxr-kicker"><?php esc_html_e('Business X-Ray Report Preview', 'business-xray-platform'); ?></p>
            <h1><?php echo esc_html($assessment->name); ?></h1>
            <p><?php printf(esc_html__('Prepared from assessment #%d on %s.', 'business-xray-platform'), (int) $assessment->id, esc_html($assessment->created_at)); ?></p>
        </div>

        <div class="bxr-grid bxr-grid-4">
            <div class="bxr-card"><span><?php esc_html_e('Business Intelligence Score', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) $assessment->overall_score); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Band', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) ($scores['band'] ?? '—')); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Priority Actions', 'business-xray-platform'); ?></span><strong><?php echo esc_html((string) count($actions)); ?></strong></div>
            <div class="bxr-card"><span><?php esc_html_e('Report Status', 'business-xray-platform'); ?></span><strong><?php esc_html_e('Draft', 'business-xray-platform'); ?></strong></div>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Executive Summary', 'business-xray-platform'); ?></h2>
            <p><?php echo esc_html((string) ($scores['recommendation'] ?? __('This report summarises the first Business X-Ray assessment and highlights the areas most worth discussing in the follow-up session.', 'business-xray-platform'))); ?></p>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Owner Context', 'business-xray-platform'); ?></h2>
            <p><?php echo $frustration !== '' ? esc_html($frustration) : esc_html__('No owner frustration was supplied in the assessment.', 'business-xray-platform'); ?></p>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('Opportunity Map', 'business-xray-platform'); ?></h2>
            <?php if (! empty($actions)) : ?>
                <table class="widefat striped">
                    <thead><tr><th><?php esc_html_e('Priority', 'business-xray-platform'); ?></th><th><?php esc_html_e('Score', 'business-xray-platform'); ?></th><th><?php esc_html_e('Suggested Focus', 'business-xray-platform'); ?></th></tr></thead>
                    <tbody>
                    <?php foreach ($actions as $action) : ?>
                        <tr>
                            <td><strong><?php echo esc_html($action['title']); ?></strong></td>
                            <td><?php echo esc_html((string) $action['score']); ?></td>
                            <td><?php echo esc_html($action['summary']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No priority actions could be generated from this assessment.', 'business-xray-platform'); ?></p>
            <?php endif; ?>
        </div>

        <div class="bxr-panel">
            <h2><?php esc_html_e('30-Day Sprint', 'business-xray-platform'); ?></h2>
            <ol>
                <li><?php esc_html_e('Book a confidential walkthrough of the findings.', 'business-xray-platform'); ?></li>
                <li><?php esc_html_e('Validate the top three priority areas with the owner or leadership team.', 'business-xray-platform'); ?></li>
                <li><?php esc_html_e('Choose one low-friction improvement to implement first.', 'business-xray-platform'); ?></li>
            </ol>
        </div>
    <?php endif; ?>
</div>
