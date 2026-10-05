<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionAssignment extends Model
{
    protected $fillable = [
        'project_id',
        'construction_firm_id',
        'construction_firm_quotation_id',
        'assigned_by',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirm::class, 'construction_firm_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirmQuotation::class, 'construction_firm_quotation_id');
    }
}
