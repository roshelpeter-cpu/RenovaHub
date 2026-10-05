<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A material line copied from an approved final design.
 * The contractor requests prices against these rows and does not invent a second list.
 */
class MaterialRequirement extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_QUOTED = 'quoted';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'project_id',
        'design_concept_id',
        'name',
        'room',
        'category',
        'quantity',
        'unit',
        'specification',
        'estimated_value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'estimated_value' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function concept(): BelongsTo
    {
        return $this->belongsTo(DesignConcept::class, 'design_concept_id');
    }
}
