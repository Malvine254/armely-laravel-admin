<?php

namespace App\Services\Mela\Tools;

use App\Support\CareerAvailability;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CurrentCareerOpportunitiesTool implements MelaTool
{
    public function name(): string
    {
        return 'check_current_career_opportunities';
    }

    public function description(): string
    {
        return 'Checks Armely\'s live career listings and deadline-based Open, Closed, or Unknown status. Always use this tool before answering whether Armely is hiring, whether any opportunities are open, or whether a specific position is accepting applications. Never infer openings from general career-page copy, old search results, or prior conversation.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => (object) [],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function execute(array $arguments, ToolContext $context): array
    {
        $careerUrl = url('/career');

        try {
            if (!Schema::hasTable('career')) {
                return $this->unavailable($careerUrl);
            }

            $positions = DB::table('career')
                ->select('job_title', 'job_location', 'job_type', 'job_deadline', 'public_token')
                ->orderByDesc('id')
                ->get()
                ->map(function ($job) use ($careerUrl) {
                    return [
                        'title' => (string) $job->job_title,
                        'location' => (string) ($job->job_location ?? ''),
                        'employment_type' => (string) ($job->job_type ?? ''),
                        'deadline' => $job->job_deadline,
                        'status' => CareerAvailability::status($job->job_deadline),
                        'url' => !empty($job->public_token)
                            ? route('job-board.show', ['publicToken' => $job->public_token])
                            : $careerUrl,
                    ];
                });
        } catch (\Throwable) {
            return $this->unavailable($careerUrl);
        }

        $open = $positions->where('status', 'Open')->values();
        $closed = $positions->where('status', 'Closed')->values();
        $unknown = $positions->where('status', 'Unknown')->values();
        $context->addSources([['title' => 'Current Armely career listings', 'url' => $careerUrl]]);

        return [
            'ok' => true,
            'status' => $unknown->isNotEmpty() && $open->isEmpty() ? 'verification_incomplete' : ($open->isNotEmpty() ? 'open_positions' : 'none_open'),
            'checked_at' => now()->toIso8601String(),
            'open_count' => $open->count(),
            'open_positions' => $open->all(),
            'closed_positions' => $closed->all(),
            'unverified_positions' => $unknown->all(),
            'career_url' => $careerUrl,
            'note' => $unknown->isNotEmpty() && $open->isEmpty()
                ? 'Some listing deadlines could not be verified. Do not claim those roles are open or say there are no openings.'
                : ($open->isEmpty()
                    ? 'No current positions are listed as open. Say so plainly; do not invent roles, disciplines, benefits, or hiring activity.'
                    : 'Only positions in open_positions are currently accepting applications. Do not describe closed or unverified positions as open.'),
        ];
    }

    private function unavailable(string $careerUrl): array
    {
        return [
            'ok' => false,
            'status' => 'unavailable',
            'open_count' => null,
            'open_positions' => [],
            'closed_positions' => [],
            'unverified_positions' => [],
            'career_url' => $careerUrl,
            'note' => 'Live career data is unavailable. Do not claim that positions are open or closed; tell the visitor you cannot verify current openings and share the career_url.',
        ];
    }
}