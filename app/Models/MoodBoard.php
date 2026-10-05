<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoodBoard extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_AWAITING = 'awaiting_approval';

    public const STATUS_REVISION = 'revision_requested';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'summary',
        'version',
        'status',
        'revision_note',
        'submitted_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MoodBoardItem::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(DesignFeedback::class);
    }

    /**
     * Only a homeowner approval makes the board the permanent design record.
     */
    public function isFinal(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->approved_at !== null;
    }

    public function canSubmit(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVISION], true);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_AWAITING => 'Awaiting Homeowner Approval',
            self::STATUS_REVISION => 'Revision Requested',
            self::STATUS_APPROVED => 'Approved / Final',
            default => 'Draft',
        };
    }
}
