<?php

namespace App\Services\Contractor;

use App\Models\Project;
use App\Models\ProjectBudgetItem;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Spent percentages are written together so a partial save cannot leave
     * the budget total and the category bars disagreeing.
     *
     * @param  array<int, int>  $spentByItemId
     */
    public function updateSpend(Project $project, array $spentByItemId): void
    {
        DB::transaction(function () use ($project, $spentByItemId) {
            $items = $project->budgetItems()->get();

            foreach ($items as $item) {
                if (! array_key_exists($item->id, $spentByItemId)) {
                    continue;
                }

                $item->update([
                    'spent_percent' => max(0, min(100, (int) $spentByItemId[$item->id])),
                ]);
            }
        });
    }

    /**
     * @param  list<array{category: string, amount: float|int|string}>  $lines
     */
    public function replaceLines(Project $project, array $lines): void
    {
        DB::transaction(function () use ($project, $lines) {
            $project->budgetItems()->delete();

            foreach (array_values($lines) as $index => $line) {
                ProjectBudgetItem::query()->create([
                    'project_id' => $project->id,
                    'category' => $line['category'],
                    'amount' => round((float) $line['amount'], 2),
                    'spent_percent' => 0,
                    'sort_order' => $index + 1,
                ]);
            }
        });
    }
}
