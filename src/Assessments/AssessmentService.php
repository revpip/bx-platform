<?php

namespace BusinessXRay\Platform\Assessments;

use BusinessXRay\Platform\Settings\Settings;

defined('ABSPATH') || exit;

final class AssessmentService
{
    public const ACTION = 'bxr_submit_assessment';

    public static function questions(): array
    {
        return [
            'owner_dependency' => [
                'label' => __('If you stepped away for two weeks, how well would the business run?', 'business-xray-platform'),
                'pillar' => 'Leadership',
                'options' => [
                    '20' => __('It would struggle quickly', 'business-xray-platform'),
                    '45' => __('Some things would continue, but key decisions would stall', 'business-xray-platform'),
                    '70' => __('It would mostly continue with some pressure points', 'business-xray-platform'),
                    '90' => __('It would run well without me day to day', 'business-xray-platform'),
                ],
            ],
            'quote_speed' => [
                'label' => __('How quickly do new enquiries usually receive a proper response or quote?', 'business-xray-platform'),
                'pillar' => 'Sales',
                'options' => [
                    '25' => __('Several days or inconsistent', 'business-xray-platform'),
                    '50' => __('Within 48 hours', 'business-xray-platform'),
                    '75' => __('Within 24 hours', 'business-xray-platform'),
                    '95' => __('Same day with a clear process', 'business-xray-platform'),
                ],
            ],
            'systems' => [
                'label' => __('How connected are your main systems and processes?', 'business-xray-platform'),
                'pillar' => 'Operations',
                'options' => [
                    '25' => __('Mostly manual, scattered or duplicated', 'business-xray-platform'),
                    '50' => __('Some systems, but plenty of workarounds', 'business-xray-platform'),
                    '75' => __('Mostly organised with a few gaps', 'business-xray-platform'),
                    '90' => __('Well documented, connected and easy to follow', 'business-xray-platform'),
                ],
            ],
            'marketing_measurement' => [
                'label' => __('How clearly do you know which marketing activity creates value?', 'business-xray-platform'),
                'pillar' => 'Marketing',
                'options' => [
                    '25' => __('We mostly guess', 'business-xray-platform'),
                    '50' => __('We track some activity but not enough', 'business-xray-platform'),
                    '75' => __('We know most of what works', 'business-xray-platform'),
                    '90' => __('We have clear data and regular review', 'business-xray-platform'),
                ],
            ],
            'customer_followup' => [
                'label' => __('How consistent is your customer follow-up?', 'business-xray-platform'),
                'pillar' => 'Customer Experience',
                'options' => [
                    '25' => __('Patchy and person-dependent', 'business-xray-platform'),
                    '50' => __('Some follow-up, but not systematic', 'business-xray-platform'),
                    '75' => __('Mostly consistent', 'business-xray-platform'),
                    '90' => __('Structured, timely and measured', 'business-xray-platform'),
                ],
            ],
            'financial_visibility' => [
                'label' => __('How clear is your view of profit, cashflow and key numbers?', 'business-xray-platform'),
                'pillar' => 'Finance',
                'options' => [
                    '25' => __('Only clear after the event', 'business-xray-platform'),
                    '50' => __('Basic visibility but not enough for quick decisions', 'business-xray-platform'),
                    '75' => __('Good visibility most of the time', 'business-xray-platform'),
                    '90' => __('Clear, timely and decision-ready', 'business-xray-platform'),
                ],
            ],
        ];
    }

    public function handle_submission(): ?array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        $action = isset($_POST['bxr_action']) ? sanitize_key(wp_unslash($_POST['bxr_action'])) : '';
        if ($action !== self::ACTION) {
            return null;
        }

        $nonce = isset($_POST['bxr_nonce']) ? sanitize_text_field(wp_unslash($_POST['bxr_nonce'])) : '';
        if (! wp_verify_nonce($nonce, self::ACTION)) {
            return [
                'success' => false,
                'message' => __('Security check failed. Please refresh the page and try again.', 'business-xray-platform'),
            ];
        }

        $data = $this->sanitize_submission($_POST);

        if ($data['business_name'] === '' || ! is_email($data['email'])) {
            return [
                'success' => false,
                'message' => __('Please enter a business name and valid email address.', 'business-xray-platform'),
            ];
        }

        $result = $this->score($data['answers']);
        $organisation_id = $this->upsert_organisation($data);
        $assessment_id = $this->save_assessment($organisation_id, $data, $result);
        $this->create_follow_up_task($organisation_id, $result);
        $this->log_activity($organisation_id, $assessment_id, $result);
        $this->notify($data, $result);

        return [
            'success' => true,
            'message' => __('Your Business X-Ray has been received.', 'business-xray-platform'),
            'score' => $result['overall'],
            'band' => $result['band'],
            'recommendation' => $result['recommendation'],
        ];
    }

    private function sanitize_submission(array $source): array
    {
        $answers = [];
        foreach (array_keys(self::questions()) as $key) {
            $answers[$key] = isset($source['bxr_answers'][$key]) ? max(0, min(100, absint($source['bxr_answers'][$key]))) : 0;
        }

        return [
            'business_name' => sanitize_text_field(wp_unslash($source['business_name'] ?? '')),
            'contact_name' => sanitize_text_field(wp_unslash($source['contact_name'] ?? '')),
            'email' => sanitize_email(wp_unslash($source['email'] ?? '')),
            'website' => esc_url_raw(wp_unslash($source['website'] ?? '')),
            'industry' => sanitize_text_field(wp_unslash($source['industry'] ?? '')),
            'biggest_frustration' => sanitize_textarea_field(wp_unslash($source['biggest_frustration'] ?? '')),
            'answers' => $answers,
        ];
    }

    private function score(array $answers): array
    {
        $questions = self::questions();
        $pillars = [];
        $total = 0;
        $count = 0;

        foreach ($questions as $key => $question) {
            $score = isset($answers[$key]) ? (int) $answers[$key] : 0;
            $pillars[$question['pillar']][] = $score;
            $total += $score;
            $count++;
        }

        $pillar_scores = [];
        foreach ($pillars as $pillar => $scores) {
            $pillar_scores[$pillar] = (int) round(array_sum($scores) / max(1, count($scores)));
        }

        $overall = (int) round($total / max(1, $count));

        if ($overall >= 80) {
            $band = __('Strong', 'business-xray-platform');
            $recommendation = __('You appear to have strong foundations. The opportunity is to target the few areas where further systemisation could unlock the next stage of growth.', 'business-xray-platform');
        } elseif ($overall >= 60) {
            $band = __('Developing', 'business-xray-platform');
            $recommendation = __('There are useful foundations in place, but several areas would benefit from clearer systems, measurement and follow-up.', 'business-xray-platform');
        } elseif ($overall >= 40) {
            $band = __('Needs Attention', 'business-xray-platform');
            $recommendation = __('The business is likely carrying avoidable friction. A focused Business X-Ray should prioritise the systems and processes creating the greatest drag.', 'business-xray-platform');
        } else {
            $band = __('High Friction', 'business-xray-platform');
            $recommendation = __('The answers suggest significant owner dependency or operational friction. The first priority should be stabilising the most critical workflows.', 'business-xray-platform');
        }

        return [
            'overall' => $overall,
            'band' => $band,
            'pillars' => $pillar_scores,
            'recommendation' => $recommendation,
        ];
    }

    private function upsert_organisation(array $data): int
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bxr_organisations';
        $now = current_time('mysql');

        $existing_id = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE contact_email = %s LIMIT 1", $data['email']));

        $payload = [
            'name' => $data['business_name'],
            'website' => $data['website'],
            'industry' => $data['industry'],
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['email'],
            'status' => 'assessment',
            'updated_at' => $now,
        ];

        if ($existing_id > 0) {
            $wpdb->update($table, $payload, ['id' => $existing_id]);
            return $existing_id;
        }

        $payload['created_at'] = $now;
        $wpdb->insert($table, $payload);

        return (int) $wpdb->insert_id;
    }

    private function save_assessment(int $organisation_id, array $data, array $result): int
    {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'bxr_assessments', [
            'organisation_id' => $organisation_id,
            'name' => $data['business_name'],
            'email' => $data['email'],
            'website' => $data['website'],
            'scores' => wp_json_encode($result),
            'answers' => wp_json_encode([
                'answers' => $data['answers'],
                'biggest_frustration' => $data['biggest_frustration'],
            ]),
            'overall_score' => $result['overall'],
            'created_at' => current_time('mysql'),
        ]);

        return (int) $wpdb->insert_id;
    }

    private function create_follow_up_task(int $organisation_id, array $result): void
    {
        global $wpdb;

        $priority = $result['overall'] < 60 ? 'high' : 'medium';

        $wpdb->insert($wpdb->prefix . 'bxr_tasks', [
            'organisation_id' => $organisation_id,
            'title' => sprintf('Follow up Business X-Ray assessment (%s)', $result['band']),
            'status' => 'open',
            'priority' => $priority,
            'due_at' => gmdate('Y-m-d H:i:s', strtotime('+2 days')),
            'created_at' => current_time('mysql'),
        ]);
    }

    private function log_activity(int $organisation_id, int $assessment_id, array $result): void
    {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'bxr_activity_log', [
            'organisation_id' => $organisation_id,
            'type' => 'assessment_submitted',
            'summary' => sprintf('Assessment #%d submitted with score %d (%s).', $assessment_id, $result['overall'], $result['band']),
            'meta' => wp_json_encode($result),
            'created_at' => current_time('mysql'),
        ]);
    }

    private function notify(array $data, array $result): void
    {
        $settings = Settings::get();
        $to = $settings['lead_email'];

        if (! is_email($to)) {
            return;
        }

        $subject = sprintf('New Business X-Ray assessment: %s', $data['business_name']);
        $body = sprintf(
            "New Business X-Ray assessment received.\n\nBusiness: %s\nContact: %s\nEmail: %s\nWebsite: %s\nIndustry: %s\nScore: %d\nBand: %s\n\nBiggest frustration:\n%s",
            $data['business_name'],
            $data['contact_name'],
            $data['email'],
            $data['website'],
            $data['industry'],
            $result['overall'],
            $result['band'],
            $data['biggest_frustration']
        );

        wp_mail($to, $subject, $body);
    }
}
