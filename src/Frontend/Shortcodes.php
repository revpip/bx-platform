<?php

namespace BusinessXRay\Platform\Frontend;

use BusinessXRay\Platform\Assessments\AssessmentService;
use BusinessXRay\Platform\Settings\Settings;

defined('ABSPATH') || exit;

final class Shortcodes
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('business_xray_landing_page', [$this, 'landing_page']);
        add_shortcode('business_xray_assessment', [$this, 'assessment']);
        add_shortcode('business_xray_pricing', [$this, 'pricing']);
        add_shortcode('business_xray_report_preview', [$this, 'report_preview']);
    }

    public function enqueue_assets(): void
    {
        wp_enqueue_style('bxr-public', BXR_PLATFORM_URL . 'assets/css/public.css', [], BXR_PLATFORM_VERSION);
    }

    public function landing_page(): string
    {
        $settings = Settings::get();
        $booking_url = ! empty($settings['booking_url']) ? $settings['booking_url'] : '#business-xray-assessment';

        ob_start();
        ?>
        <section class="bxr-public bxr-landing" aria-label="<?php esc_attr_e('Business X-Ray landing section', 'business-xray-platform'); ?>">
            <div class="bxr-public__hero">
                <p class="bxr-public__kicker"><?php esc_html_e('Business X-Ray™', 'business-xray-platform'); ?></p>
                <h2><?php echo esc_html($settings['headline']); ?></h2>
                <p><?php esc_html_e('A confidential diagnostic for owner-managed businesses that want to find hidden friction, wasted time, weak systems and missed opportunities.', 'business-xray-platform'); ?></p>
                <div class="bxr-public__actions">
                    <a class="bxr-public__button" href="#business-xray-assessment"><?php esc_html_e('Start the assessment', 'business-xray-platform'); ?></a>
                    <a class="bxr-public__button bxr-public__button--ghost" href="<?php echo esc_url($booking_url); ?>"><?php esc_html_e('Book a confidential call', 'business-xray-platform'); ?></a>
                </div>
            </div>
            <div class="bxr-public__grid">
                <article><strong><?php esc_html_e('Find friction', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Identify repeated work, slow handovers and owner dependency.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('Prioritise action', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Focus on the improvements most likely to create meaningful value.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('Build confidence', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Leave with a clearer view of what should happen next.', 'business-xray-platform'); ?></span></article>
            </div>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    public function assessment(): string
    {
        $service = new AssessmentService();
        $submission = $service->handle_submission();
        $questions = AssessmentService::questions();

        ob_start();
        ?>
        <section id="business-xray-assessment" class="bxr-public bxr-assessment">
            <p class="bxr-public__kicker"><?php esc_html_e('Free Assessment', 'business-xray-platform'); ?></p>
            <h2><?php esc_html_e('Start with a simple Business X-Ray.', 'business-xray-platform'); ?></h2>
            <p><?php esc_html_e('Answer a few practical questions and receive an immediate Business Intelligence Score. Your answers are also saved securely for follow-up.', 'business-xray-platform'); ?></p>

            <?php if (is_array($submission)) : ?>
                <div class="bxr-result <?php echo $submission['success'] ? 'bxr-result--success' : 'bxr-result--error'; ?>">
                    <strong><?php echo esc_html($submission['message']); ?></strong>
                    <?php if ($submission['success']) : ?>
                        <span><?php printf(esc_html__('Score: %1$d / 100 - %2$s', 'business-xray-platform'), (int) $submission['score'], esc_html($submission['band'])); ?></span>
                        <p><?php echo esc_html($submission['recommendation']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (! is_array($submission) || empty($submission['success'])) : ?>
                <form method="post" class="bxr-assessment-form">
                    <?php wp_nonce_field(AssessmentService::ACTION, 'bxr_nonce'); ?>
                    <input type="hidden" name="bxr_action" value="<?php echo esc_attr(AssessmentService::ACTION); ?>">

                    <div class="bxr-form-grid">
                        <label><?php esc_html_e('Business name', 'business-xray-platform'); ?><input type="text" name="business_name" required></label>
                        <label><?php esc_html_e('Your name', 'business-xray-platform'); ?><input type="text" name="contact_name"></label>
                        <label><?php esc_html_e('Email address', 'business-xray-platform'); ?><input type="email" name="email" required></label>
                        <label><?php esc_html_e('Website', 'business-xray-platform'); ?><input type="url" name="website"></label>
                        <label><?php esc_html_e('Industry', 'business-xray-platform'); ?><input type="text" name="industry"></label>
                    </div>

                    <label class="bxr-full-field"><?php esc_html_e('What is the biggest frustration in the business right now?', 'business-xray-platform'); ?><textarea name="biggest_frustration" rows="4"></textarea></label>

                    <div class="bxr-question-list">
                        <?php foreach ($questions as $key => $question) : ?>
                            <fieldset class="bxr-question">
                                <legend><?php echo esc_html($question['label']); ?></legend>
                                <span class="bxr-question__pillar"><?php echo esc_html($question['pillar']); ?></span>
                                <?php foreach ($question['options'] as $score => $label) : ?>
                                    <label class="bxr-option">
                                        <input type="radio" name="bxr_answers[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($score); ?>" required>
                                        <span><?php echo esc_html($label); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </fieldset>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="bxr-public__button"><?php esc_html_e('Calculate my Business X-Ray score', 'business-xray-platform'); ?></button>
                </form>
            <?php endif; ?>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    public function pricing(): string
    {
        ob_start();
        ?>
        <section class="bxr-public bxr-pricing">
            <p class="bxr-public__kicker"><?php esc_html_e('Founding Pilot', 'business-xray-platform'); ?></p>
            <h2><?php esc_html_e('A practical diagnostic before a major rebuild.', 'business-xray-platform'); ?></h2>
            <div class="bxr-public__grid">
                <article><strong><?php esc_html_e('Free', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Initial website and friction scan.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('Business X-Ray', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Full assessment, findings report and 90-day roadmap.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('Growth Partner', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Ongoing review, implementation support and progress tracking.', 'business-xray-platform'); ?></span></article>
            </div>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    public function report_preview(): string
    {
        ob_start();
        ?>
        <section class="bxr-public bxr-report-teaser">
            <p class="bxr-public__kicker"><?php esc_html_e('Report Preview', 'business-xray-platform'); ?></p>
            <h2><?php esc_html_e('A report a business owner can actually use.', 'business-xray-platform'); ?></h2>
            <p><?php esc_html_e('Business X-Ray reports are designed to be clear, practical and board-ready. They explain what was found, why it matters and what should happen next.', 'business-xray-platform'); ?></p>
            <div class="bxr-public__grid">
                <article><strong><?php esc_html_e('Executive Summary', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Plain-English diagnosis of the main pattern affecting the business.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('Opportunity Map', 'business-xray-platform'); ?></strong><span><?php esc_html_e('Prioritised improvements by value, urgency and effort.', 'business-xray-platform'); ?></span></article>
                <article><strong><?php esc_html_e('30-Day Sprint', 'business-xray-platform'); ?></strong><span><?php esc_html_e('A focused action plan to create progress without overwhelming the owner.', 'business-xray-platform'); ?></span></article>
            </div>
        </section>
        <?php
        return (string) ob_get_clean();
    }
}
