<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * The dashboard is an operations summary.
     * Project cards stay on My Projects so the two pages do not repeat each other.
     *
     * @return array<string, mixed>
     */
    public function overview(User $homeowner): array
    {
        $projects = $homeowner->projects()
            ->with(['progressStages', 'designer.professionalProfile', 'contractor.professionalProfile'])
            ->latest()
            ->get();

        $projectIds = $projects->pluck('id');

        $pendingQuotations = Quotation::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', Quotation::STATUS_PENDING)
            ->with('project')
            ->latest()
            ->get();

        $openChanges = ChangeRequest::query()
            ->whereIn('project_id', $projectIds)
            ->whereIn('status', ChangeRequest::openStatuses())
            ->with('project')
            ->latest()
            ->get();

        $pendingPayments = Payment::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', Payment::STATUS_PENDING)
            ->with('project')
            ->latest()
            ->get();

        $unreadMessages = $homeowner->projects()
            ->with(['messages' => function ($query) use ($homeowner) {
                $query->whereNull('read_at')->where('sender_id', '!=', $homeowner->id)->latest();
            }])
            ->get()
            ->flatMap(fn (Project $project) => $project->messages->each(fn ($message) => $message->setRelation('project', $project)));

        return [
            'counts' => [
                'active' => $projects->where('status', Project::STATUS_IN_PROGRESS)->count(),
                'quotations' => $pendingQuotations->count(),
                'payments' => $pendingPayments->count(),
                'changes' => $openChanges->count(),
                'messages' => $unreadMessages->count(),
            ],
            'attention' => $this->attention($pendingQuotations, $openChanges, $pendingPayments, $unreadMessages),
            'projects' => $projects,
            'activity' => $homeowner->projects()
                ->with('activity')
                ->get()
                ->flatMap->activity
                ->sortByDesc('created_at')
                ->take(8)
                ->values(),
            'milestones' => $homeowner->projects()
                ->with('milestones')
                ->get()
                ->flatMap->milestones
                ->whereNull('completed_at')
                ->sortBy('due_on')
                ->take(5)
                ->values(),
            'upcomingPayments' => $pendingPayments->take(4),
            'finance' => [
                'budget' => (float) $projects->sum(fn (Project $project) => (float) $project->estimated_budget),
                'approved' => (float) Quotation::query()->whereIn('project_id', $projectIds)->where('status', Quotation::STATUS_APPROVED)->sum('total'),
                'paid' => (float) Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PAID)->sum('amount'),
                'outstanding' => max(0, (float) Quotation::query()->whereIn('project_id', $projectIds)->where('status', Quotation::STATUS_APPROVED)->sum('total')
                    - (float) Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PAID)->sum('amount')),
            ],
        ];
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function attention(Collection $quotations, Collection $changes, Collection $payments, Collection $messages): Collection
    {
        $items = collect();

        foreach ($quotations as $quotation) {
            $items->push([
                'title' => 'Quotation awaiting approval',
                'project' => $quotation->project->name,
                'body' => $quotation->number.' from the contractor is waiting for your decision.',
                'time' => $quotation->created_at->diffForHumans(),
                'action' => 'Review',
                'url' => route('homeowner.quotations.show', [$quotation->project, $quotation]),
            ]);
        }

        foreach ($changes as $change) {
            $items->push([
                'title' => 'Change request awaiting your decision',
                'project' => $change->project->name,
                'body' => $change->title,
                'time' => $change->created_at->diffForHumans(),
                'action' => 'Open',
                'url' => route('homeowner.change-requests.show', [$change->project, $change]),
            ]);
        }

        foreach ($payments as $payment) {
            $items->push([
                'title' => 'Payment due',
                'project' => $payment->project->name,
                'body' => $payment->reference.' is still outstanding.',
                'time' => $payment->created_at->diffForHumans(),
                'action' => 'View',
                'url' => route('homeowner.payments.show', [$payment->project, $payment]),
            ]);
        }

        foreach ($messages->take(4) as $message) {
            $items->push([
                'title' => 'New project message',
                'project' => $message->project->name,
                'body' => str($message->body)->limit(90)->toString(),
                'time' => $message->created_at->diffForHumans(),
                'action' => 'Reply',
                'url' => route('homeowner.projects.messages', $message->project),
            ]);
        }

        return $items->take(6)->values();
    }
}
