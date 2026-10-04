<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Home is a magazine-style overview. Counts and the active card come from
     * owned projects so a posted user_id cannot change what is shown.
     *
     * @return array<string, mixed>
     */
    public function home(User $homeowner): array
    {
        $projects = $homeowner->projects()
            ->with(['progressStages', 'designer.professionalProfile', 'contractor.professionalProfile'])
            ->latest()
            ->get();

        $active = $projects->first(fn (Project $project) => $project->status === Project::STATUS_IN_PROGRESS && $project->name === 'Modern Villa Renovation')
            ?? $projects->firstWhere('status', Project::STATUS_IN_PROGRESS)
            ?? $projects->first();

        if ($active !== null) {
            $active->designer?->professionalProfile?->setRelation('user', $active->designer);
            $active->contractor?->professionalProfile?->setRelation('user', $active->contractor);
        }

        return [
            'firstName' => str($homeowner->name)->before(' ')->upper()->toString(),
            'greeting' => now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening'),
            'summary' => [
                'total' => $projects->count(),
                'in_progress' => $projects->where('status', Project::STATUS_IN_PROGRESS)->count(),
                'completed' => $projects->where('status', Project::STATUS_COMPLETED)->count(),
                'value' => (float) $projects->sum(fn (Project $project) => (float) $project->estimated_budget),
            ],
            'activeProject' => $active,
            'activityGroups' => $this->activityGroups($homeowner),
            'actions' => $this->homeActions($homeowner),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function overview(User $homeowner): array
    {
        return $this->home($homeowner);
    }

    /**
     * @return Collection<int, array{label: string, items: Collection}>
     */
    private function activityGroups(User $homeowner): Collection
    {
        $entries = $homeowner->projects()
            ->with(['activity' => fn ($query) => $query->latest()->limit(8)])
            ->get()
            ->flatMap->activity
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        return $entries->groupBy(function ($entry) {
            $date = Carbon::parse($entry->created_at)->startOfDay();

            if ($date->isToday()) {
                return 'Today';
            }

            if ($date->isYesterday()) {
                return 'Yesterday';
            }

            return $date->format('j M Y');
        });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function homeActions(User $homeowner): Collection
    {
        $projectIds = $homeowner->projects()->select('id');
        $items = collect();

        foreach (Quotation::query()->whereIn('project_id', $projectIds)->where('status', Quotation::STATUS_PENDING)->with('project')->latest()->get() as $quotation) {
            $items->push([
                'icon' => 'quote',
                'title' => 'Quotation awaiting approval',
                'body' => $quotation->description,
                'amount' => 'LKR '.number_format((float) $quotation->total, 0),
                'url' => route('homeowner.quotations.show', [$quotation->project, $quotation]),
            ]);
        }

        foreach ($homeowner->projects()->with('moodBoard.feedback')->get() as $project) {
            $feedback = $project->moodBoard?->feedback?->first();
            if ($feedback && $project->moodBoard?->approved_at === null) {
                $items->push([
                    'icon' => 'design',
                    'title' => 'Design awaiting approval',
                    'body' => $feedback->title,
                    'amount' => null,
                    'url' => route('homeowner.projects.mood-board', $project),
                ]);
            }
        }

        foreach (Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PENDING)->with('project')->latest()->get() as $payment) {
            $items->push([
                'icon' => 'pay',
                'title' => 'Payment pending',
                'body' => $payment->notes ?: $payment->project->name,
                'amount' => 'LKR '.number_format((float) $payment->amount, 0),
                'url' => route('homeowner.payments.show', [$payment->project, $payment]),
            ]);
        }

        foreach (ChangeRequest::query()->whereIn('project_id', $projectIds)->whereIn('status', ChangeRequest::openStatuses())->with('project')->latest()->get() as $change) {
            $items->push([
                'icon' => 'change',
                'title' => 'Change request',
                'body' => $change->title,
                'amount' => null,
                'url' => route('homeowner.change-requests.show', [$change->project, $change]),
            ]);
        }

        return $items->take(4)->values();
    }
}
