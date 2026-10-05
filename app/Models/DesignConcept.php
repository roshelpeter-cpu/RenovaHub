<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignConcept extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_AWAITING = 'awaiting_approval';

    public const STATUS_REVISION = 'revision_requested';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'project_id',
        'designer_id',
        'title',
        'description',
        'notes',
        'status',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DesignConceptFile::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_AWAITING, self::STATUS_SUBMITTED => 'Awaiting Approval',
            self::STATUS_REVISION => 'Revision Requested',
            self::STATUS_APPROVED => 'Approved',
            default => 'Draft',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION], true);
    }
}
