<?php

namespace App\Services\Contractor;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Line totals are calculated here so a tampered item total from the
     * browser cannot change the quotation the homeowner is asked to approve.
     *
     * @param  list<array{item: string, category: string, description?: string|null, quantity: float|int|string, unit_cost: float|int|string}>  $items
     */
    public function saveDraft(User $contractor, Project $project, array $items, string $description, ?string $validUntil, ?Quotation $quotation = null): Quotation
    {
        return DB::transaction(function () use ($contractor, $project, $items, $description, $validUntil, $quotation) {
            $quotation ??= $project->quotations()->create([
                'contractor_id' => $contractor->id,
                'number' => $this->nextNumber($project),
                'description' => $description,
                'status' => Quotation::STATUS_DRAFT,
                'valid_until' => $validUntil,
            ]);

            abort_unless((int) $quotation->contractor_id === (int) $contractor->id, 403);
            abort_unless($quotation->status === Quotation::STATUS_DRAFT, 403);

            $quotation->items()->delete();

            $materials = 0.0;
            $labour = 0.0;
            $additional = 0.0;

            foreach ($items as $row) {
                $quantity = round((float) $row['quantity'], 2);
                $unit = round((float) $row['unit_cost'], 2);
                $line = round($quantity * $unit, 2);
                $category = $row['category'] ?? 'materials';

                $quotation->items()->create([
                    'item' => $row['item'],
                    'category' => $category,
                    'description' => $row['description'] ?? null,
                    'quantity' => $quantity,
                    'unit_cost' => $unit,
                    'total' => $line,
                ]);

                if ($category === 'labour') {
                    $labour += $line;
                } elseif (in_array($category, ['equipment', 'other'], true)) {
                    $additional += $line;
                } else {
                    $materials += $line;
                }
            }

            $subtotal = $materials + $labour + $additional;

            $quotation->update([
                'description' => $description,
                'valid_until' => $validUntil,
                'materials' => $materials,
                'labour' => $labour,
                'additional_costs' => $additional,
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);

            return $quotation->fresh('items');
        });
    }

    /**
     * Submission hands the quotation to the homeowner. It does not approve it.
     */
    public function submit(User $contractor, Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($contractor, $quotation) {
            $quotation->update(['status' => Quotation::STATUS_SUBMITTED]);

            $homeowner = $quotation->project->homeowner;

            if ($homeowner) {
                $this->notifications->notify(
                    $homeowner,
                    'Quotation '.$quotation->number.' submitted',
                    $contractor->name.' submitted a project quotation for '.$quotation->project->name.'.',
                    'quotations',
                    route('homeowner.quotations.show', [$quotation->project, $quotation]),
                );
            }

            return $quotation->fresh();
        });
    }

    private function nextNumber(Project $project): string
    {
        $count = Quotation::query()->where('project_id', $project->id)->count() + 1;

        return 'QTN-'.str_pad((string) $project->id, 3, '0', STR_PAD_LEFT).'-'.str_pad((string) $count, 2, '0', STR_PAD_LEFT);
    }
}
