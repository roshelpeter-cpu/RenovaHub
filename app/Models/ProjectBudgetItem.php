<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBudgetItem extends Model
{
    protected $fillable = [
        'project_id',
        'category',
        'amount',
        'spent_percent',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'spent_percent' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function shareOf(float $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        return (int) round(((float) $this->amount / $total) * 100);
    }

    /**
     * In-progress bars track how much of this line has been spent.
     * Completed projects display the share of the final cost instead.
     */
    public function spentAmount(): float
    {
        return round(((float) $this->amount) * (min(100, max(0, (int) $this->spent_percent)) / 100), 2);
    }
}
