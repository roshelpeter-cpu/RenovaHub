<?php

namespace App\Services;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ChangeRequestService
{
    /**
     * Change requests are collected through the homeowner's projects first.
     * A project id outside that set is a 404, not an empty page.
     *
     * @param  array{project?: int|null, status?: string|null, category?: string|null, search?: string|null, from?: string|null, to?: string|null}  $filters
     * @return array{groups: LengthAwarePaginator, projects: \Illuminate\Support\Collection, summary: array<string, int>, filters: array}
     */
    public function homeownerBoard(User $homeowner, array $filters): array
    {
        $projects = $homeowner->projects()->orderBy('name')->get();
        $ownedIds = $projects->pluck('id');

        if (! empty($filters['project']) && ! $ownedIds->contains((int) $filters['project'])) {
            abort(404);
        }

        $summaryBase = ChangeRequest::query()->whereIn('project_id', $ownedIds);
        $matchingIds = $this->constrain(ChangeRequest::query()->whereIn('project_id', $ownedIds), $filters)
            ->distinct()
            ->pluck('project_id');

        $groups = Project::query()
            ->whereIn('id', $matchingIds)
            ->with(['budgetItems', 'changeRequests' => function ($query) use ($filters) {
                $query->with('requester');
                $this->constrain($query, $filters)->latest('id');
            }])
            ->orderBy('name')
            ->paginate(4)
            ->withQueryString();

        return [
            'groups' => $groups,
            'projects' => $projects,
            'summary' => [
                'total' => (clone $summaryBase)->count(),
                'pending' => (clone $summaryBase)->where('status', ChangeRequest::STATUS_SUBMITTED)->count(),
                'approved' => (clone $summaryBase)->whereIn('status', [ChangeRequest::STATUS_APPROVED, ChangeRequest::STATUS_IMPLEMENTED])->count(),
                'rejected' => (clone $summaryBase)->where('status', ChangeRequest::STATUS_REJECTED)->count(),
            ],
            'filters' => $filters,
        ];
    }

    /**
     * Accepts a change-request query or the eager-load relation. Both expose where().
     *
     * @param  Builder<ChangeRequest>|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  array{project?: int|null, status?: string|null, category?: string|null, search?: string|null, from?: string|null, to?: string|null}  $filters
     */
    private function constrain(Builder|\Illuminate\Database\Eloquent\Relations\Relation $query, array $filters): Builder|\Illuminate\Database\Eloquent\Relations\Relation
    {
        if (! empty($filters['project'])) {
            $query->where('project_id', (int) $filters['project']);
        }

        if (! empty($filters['status'])) {
            $query->where(function ($inner) use ($filters) {
                match ($filters['status']) {
                    'pending' => $inner->where('status', ChangeRequest::STATUS_SUBMITTED),
                    'in_review' => $inner->whereIn('status', [
                        ChangeRequest::STATUS_UNDER_REVIEW,
                        ChangeRequest::STATUS_COST_PROVIDED,
                        ChangeRequest::STATUS_AWAITING_APPROVAL,
                    ]),
                    'approved' => $inner->whereIn('status', [ChangeRequest::STATUS_APPROVED, ChangeRequest::STATUS_IMPLEMENTED]),
                    'rejected' => $inner->where('status', ChangeRequest::STATUS_REJECTED),
                    default => $inner->where('status', $filters['status']),
                };
            });
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }

        if (! empty($filters['search'])) {
            $term = '%'.addcslashes($filters['search'], '%_\\').'%';
            $query->where(function ($inner) use ($term) {
                $inner->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('project', fn ($project) => $project->where('name', 'like', $term));
            });
        }

        return $query;
    }
}
