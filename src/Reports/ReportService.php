<?php

namespace BusinessXRay\Platform\Reports;

defined('ABSPATH') || exit;

final class ReportService
{
    public function load_assessment(int $assessment_id): ?object
    {
        global $wpdb;

        if ($assessment_id <= 0) {
            return null;
        }

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}bxr_assessments WHERE id = %d", $assessment_id)) ?: null;
    }

    public function decode_scores(object $assessment): array
    {
        $scores = json_decode((string) $assessment->scores, true);
        return is_array($scores) ? $scores : [];
    }

    public function decode_answers(object $assessment): array
    {
        $answers = json_decode((string) $assessment->answers, true);
        return is_array($answers) ? $answers : [];
    }

    public function priority_actions(array $scores): array
    {
        $pillars = is_array($scores['pillars'] ?? null) ? $scores['pillars'] : [];
        asort($pillars);
        $weakest = array_slice($pillars, 0, 3, true);

        $actions = [];
        foreach ($weakest as $pillar => $score) {
            $actions[] = [
                'title' => sprintf('Improve %s', $pillar),
                'score' => (int) $score,
                'summary' => $this->action_summary((string) $pillar),
            ];
        }

        return $actions;
    }

    private function action_summary(string $pillar): string
    {
        $summaries = [
            'Leadership' => 'Reduce owner dependency by clarifying responsibilities, decisions and escalation routes.',
            'Sales' => 'Tighten enquiry response, quoting and follow-up so fewer opportunities drift.',
            'Marketing' => 'Connect activity to measurement so spend, content and campaigns can be judged properly.',
            'Operations' => 'Document recurring workflows and remove avoidable duplication or manual workarounds.',
            'Customer Experience' => 'Create a more consistent follow-up and review process after enquiries or delivery.',
            'Finance' => 'Improve visibility of the numbers needed for faster and calmer decisions.',
        ];

        return $summaries[$pillar] ?? 'Review this area and identify the highest-friction process to improve first.';
    }
}
