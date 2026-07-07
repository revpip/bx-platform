<?php

namespace BusinessXRay\Platform\Frontend;

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
        ob_start();
        ?>
        <section id="business-xray-assessment" class="bxr-public bxr-assessment-placeholder">
            <p class="bxr-public__kicker"><?php esc_html_e('Free Assessment', 'business-xray-platform'); ?></p>
            <h2><?php esc_html_e('Start with a simple Business X-Ray.', 'business-xray-platform'); ?></h2>
            <p><?php esc_html_e('The production assessment engine is the next platform module. This placeholder confirms shortcode wiring and provides the public journey for Sprint 1.', 'business-xray-platform'); ?></p>
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
}
