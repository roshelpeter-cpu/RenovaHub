<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeRequest extends Model
{
    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_COST_PROVIDED = 'cost_provided';

    public const STATUS_AWAITING_APPROVAL = 'awaiting_approval';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_IMPLEMENTED = 'implemented';

    protected $fillable = [
        'project_id',
        'requested_by',
        'title',
        'description',
        'reason',
        'category',
        'priority',
        'status',
        'approved_at',
        'contractor_response',
        'designer_response',
        'cost_impact',
        'timeline_impact',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'cost_impact' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'design' => 'Design',
            'interior' => 'Interior',
            'material' => 'Materials',
            'electrical' => 'Electrical',
            'fixtures' => 'Fixtures',
            'carpentry' => 'Carpentry',
            'structural' => 'Structural',
            'construction' => 'Construction',
            'scope' => 'Scope',
            'budget' => 'Budget',
            'timeline' => 'Timeline',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? ucfirst((string) $this->category);
    }

    public function reviewLabel(): string
    {
        return match ($this->status) {
            self::STATUS_SUBMITTED => 'Pending',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_APPROVED, self::STATUS_IMPLEMENTED => 'Approved',
            default => 'In Review',
        };
    }

    /**
     * @return list<string>
     */
    public static function openStatuses(): array
    {
        return [
            self::STATUS_SUBMITTED,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_COST_PROVIDED,
            self::STATUS_AWAITING_APPROVAL,
        ];
    }

    public function statusLabel(): string
    {
        return str($this->status)->replace('_', ' ')->title()->toString();
    }

    /**
     * The number is derived from the primary key so it cannot drift from the row.
     */
    public function reference(): string
    {
        return 'CR-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
