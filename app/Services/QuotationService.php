<?php

namespace App\Services;

use App\Events\QuotationDecided;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Totals are calculated here so a tampered line total cannot be posted by the browser.
     */
    public function recalculate(Quotation $quotation): Quotation
    {
        $materials = (float) $quotation->materials;
        $labour = (float) $quotation->labour;
        $additional = (float) $quotation->additional_costs;
        $discount = (float) $quotation->discount;
        $subtotal = $materials + $labour + $additional;

        $quotation->update([
            'subtotal' => $subtotal,
            'total' => max(0, $subtotal - $discount),
        ]);

        return $quotation->refresh();
    }

    /**
     * Quotations are grouped by the homeowner's own projects. The project
     * filter is rejected unless that project id is already in the owned set.
     *
     * @param  array{project?: int|null, status?: string|null, professional?: int|null, search?: string|null, from?: string|null, to?: string|null}  $filters
     * @return array{groups: LengthAwarePaginator, projects: \Illuminate\Support\Collection, professionals: \Illuminate\Support\Collection, summary: array<string, int>, filters: array}
     */
    public function homeownerBoard(User $homeowner, array $filters): array
    {
        $projects = $homeowner->projects()->orderBy('name')->get();
        $ownedIds = $projects->pluck('id');

        if (! empty($filters['project']) && ! $ownedIds->contains((int) $filters['project'])) {
            abort(404);
        }

        $allowedProfessionals = User::query()
            ->whereIn('id', Quotation::query()->whereIn('project_id', $ownedIds)->whereNotNull('contractor_id')->distinct()->pluck('contractor_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        if (! empty($filters['professional']) && ! $allowedProfessionals->contains('id', (int) $filters['professional'])) {
            abort(404);
        }

        $summaryBase = Quotation::query()
            ->whereIn('project_id', $ownedIds)
            ->where('status', '!=', Quotation::STATUS_DRAFT);

        $matchingIds = $this->constrain(Quotation::query()->whereIn('project_id', $ownedIds), $filters)
            ->distinct()
            ->pluck('project_id');

        $groups = Project::query()
            ->whereIn('id', $matchingIds)
            ->with(['budgetItems', 'quotations' => function ($query) use ($filters) {
                $query->with(['contractor.professionalProfile']);
                $this->constrain($query, $filters)->latest('id');
            }])
            ->orderBy('name')
            ->paginate(4)
            ->withQueryString();

        return [
            'groups' => $groups,
            'projects' => $projects,
            'professionals' => $allowedProfessionals,
            'summary' => [
                'total' => (clone $summaryBase)->count(),
                'pending' => (clone $summaryBase)->whereIn('status', [Quotation::STATUS_PENDING, Quotation::STATUS_SUBMITTED])->count(),
                'approved' => (clone $summaryBase)->where('status', Quotation::STATUS_APPROVED)->count(),
                'rejected' => (clone $summaryBase)->where('status', Quotation::STATUS_REJECTED)->count(),
            ],
            'filters' => $filters,
        ];
    }

    /**
     * Accepts a quotation query or the eager-load relation. Both expose where().
     *
     * @param  Builder<Quotation>|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  array{project?: int|null, status?: string|null, professional?: int|null, search?: string|null, from?: string|null, to?: string|null}  $filters
     */
    private function constrain(Builder|\Illuminate\Database\Eloquent\Relations\Relation $query, array $filters): Builder|\Illuminate\Database\Eloquent\Relations\Relation
    {
        if (! empty($filters['project'])) {
            $query->where('project_id', (int) $filters['project']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['professional'])) {
            $query->where('contractor_id', (int) $filters['professional']);
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
                $inner->where('number', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('project', fn ($project) => $project->where('name', 'like', $term));
            });
        }

        // Drafts stay on the contractor workspace until they are submitted.
        $query->where('status', '!=', Quotation::STATUS_DRAFT);

        return $query;
    }

    public function decide(Quotation $quotation, User $homeowner, string $status, ?string $note = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $homeowner, $status, $note) {
            $locked = (bool) ($quotation->project->workspace_meta['budget_locked'] ?? false);

            $quotation->update([
                'status' => $status,
                'notes' => ($note !== null && $note !== '') ? $note : $quotation->notes,
                'approved_at' => $status === Quotation::STATUS_APPROVED ? now() : null,
            ]);

            // A locked or completed budget is historical. Approving one quote must not replace it.
            if ($status === Quotation::STATUS_APPROVED && ! $locked && ! $quotation->project->isClosedRecord()) {
                $quotation->project->update(['current_budget' => $quotation->total]);
                app(PaymentService::class)->openForQuotation($quotation->fresh());
            }

            $this->notifications->notify(
                $homeowner,
                'Quotation '.$quotation->number.' updated',
                'The quotation is now '.$quotation->statusLabel().'.',
                'quotations',
                route('homeowner.quotations.show', [$quotation->project, $quotation]),
            );

            QuotationDecided::dispatch($quotation->fresh(), $status);

            return $quotation->fresh();
        });
    }
}
