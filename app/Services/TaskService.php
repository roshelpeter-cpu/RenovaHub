<?php

namespace App\Services;

use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class TaskService
{
    /**
     * Global tasks always start from the homeowner's projects. A posted
     * project id is applied only after that scope, so another homeowner's
     * tasks cannot be reached by changing the query string.
     *
     * @param  array{project?: int|null, status?: string|null, category?: string|null, search?: string|null}  $filters
     * @return array{tasks: LengthAwarePaginator, projects: \Illuminate\Support\Collection, summary: object, filters: array}
     */
    public function globalIndex(User $homeowner, array $filters): array
    {
        $projects = $homeowner->projects()->orderBy('name')->get(['id', 'name', 'city', 'cover_image']);
        $ownedIds = $projects->pluck('id');

        $base = ProjectTask::query()->whereIn('project_id', $ownedIds);
        $summary = $this->summary(clone $base);

        $tasks = $this->applyFilters(clone $base, $filters, $ownedIds)
            ->with(['project', 'assignee.professionalProfile'])
            ->orderByRaw("case when status = 'completed' then 1 else 0 end")
            ->orderBy('due_on')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return [
            'tasks' => $tasks,
            'projects' => $projects,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * @param  Builder<ProjectTask>  $query
     */
    private function summary(Builder $query): object
    {
        $row = $query->selectRaw(
            'count(*) as total,
            sum(case when status = ? then 1 else 0 end) as in_progress,
            sum(case when status = ? then 1 else 0 end) as completed,
            sum(case when status = ? and (due_on is null or due_on >= ?) then 1 else 0 end) as not_started,
            sum(case when status = ? and due_on is not null and due_on < ? then 1 else 0 end) as overdue',
            [
                ProjectTask::STATUS_IN_PROGRESS,
                ProjectTask::STATUS_COMPLETED,
                ProjectTask::STATUS_PENDING,
                now()->toDateString(),
                ProjectTask::STATUS_PENDING,
                now()->toDateString(),
            ],
        )->first();

        return (object) [
            'total' => (int) ($row->total ?? 0),
            'in_progress' => (int) ($row->in_progress ?? 0),
            'completed' => (int) ($row->completed ?? 0),
            'not_started' => (int) ($row->not_started ?? 0),
            'overdue' => (int) ($row->overdue ?? 0),
        ];
    }

    /**
     * @param  Builder<ProjectTask>  $query
     * @param  array{project?: int|null, status?: string|null, category?: string|null, search?: string|null}  $filters
     * @param  \Illuminate\Support\Collection<int, int>  $ownedIds
     * @return Builder<ProjectTask>
     */
    private function applyFilters(Builder $query, array $filters, $ownedIds): Builder
    {
        $projectId = (int) ($filters['project'] ?? 0);
        if ($projectId > 0 && $ownedIds->contains($projectId)) {
            $query->where('project_id', $projectId);
        }

        $status = (string) ($filters['status'] ?? '');
        if ($status === 'not_started') {
            $query->where('status', ProjectTask::STATUS_PENDING)
                ->where(function (Builder $inner) {
                    $inner->whereNull('due_on')->orWhereDate('due_on', '>=', now()->toDateString());
                });
        } elseif ($status === 'overdue') {
            $query->where('status', ProjectTask::STATUS_PENDING)
                ->whereNotNull('due_on')
                ->whereDate('due_on', '<', now()->toDateString());
        } elseif (in_array($status, [ProjectTask::STATUS_IN_PROGRESS, ProjectTask::STATUS_COMPLETED], true)) {
            $query->where('status', $status);
        }

        $category = (string) ($filters['category'] ?? '');
        if (array_key_exists($category, ProjectTask::categories())) {
            $query->where('category', $category);
        }

        $search = trim((string) ($filters['search'] ?? ''));
        if ($search !== '') {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $inner) use ($term) {
                $inner->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('project', fn (Builder $project) => $project->where('name', 'like', $term))
                    ->orWhereHas('assignee', fn (Builder $assignee) => $assignee->where('name', 'like', $term));
            });
        }

        return $query;
    }

    /**
     * @param  array{project?: int|null, status?: string|null, category?: string|null, search?: string|null}  $input
     * @return array{project: int|null, status: string, category: string, search: string}
     */
    public function filters(User $homeowner, array $input): array
    {
        $projectId = (int) ($input['project'] ?? 0);
        if ($projectId > 0 && ! $homeowner->projects()->whereKey($projectId)->exists()) {
            abort(404);
        }

        return [
            'project' => $projectId > 0 ? $projectId : null,
            'status' => array_key_exists((string) ($input['status'] ?? ''), ProjectTask::filterStatuses()) ? (string) $input['status'] : '',
            'category' => array_key_exists((string) ($input['category'] ?? ''), ProjectTask::categories()) ? (string) $input['category'] : '',
            'search' => trim((string) ($input['search'] ?? '')),
        ];
    }
}
