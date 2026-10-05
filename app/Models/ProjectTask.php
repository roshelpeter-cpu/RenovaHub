<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'project_id',
        'assignee_id',
        'assignee_label',
        'name',
        'description',
        'notes',
        'category',
        'status',
        'priority',
        'started_on',
        'due_on',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'due_on' => 'date',
            'progress' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'design' => 'Design',
            'planning' => 'Planning',
            'procurement' => 'Procurement',
            'construction' => 'Construction',
            'interior' => 'Interior',
            'materials' => 'Materials',
            'inspection' => 'Inspection',
        ];
    }

    /**
     * Filter values the global Tasks page accepts. Overdue is derived from
     * the due date so it cannot be stored as a conflicting status.
     *
     * @return array<string, string>
     */
    public static function filterStatuses(): array
    {
        return [
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'overdue' => 'Overdue',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? ucfirst((string) $this->category);
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->due_on !== null
            && $this->due_on->lt(now()->startOfDay());
    }

    public function displayStatus(): string
    {
        if ($this->isOverdue()) {
            return 'overdue';
        }

        return $this->status === self::STATUS_PENDING ? 'not_started' : (string) $this->status;
    }

    public function statusLabel(): string
    {
        return match ($this->displayStatus()) {
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'overdue' => 'Overdue',
            'blocked' => 'Blocked',
            default => 'Not Started',
        };
    }

    public function contractorStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_BLOCKED => 'Blocked',
            'not_started' => 'Not Started',
            default => 'Pending',
        };
    }

    public function assigneeLabel(): string
    {
        if (is_string($this->assignee_label) && $this->assignee_label !== '') {
            return $this->assignee_label;
        }

        $profile = $this->assignee?->professionalProfile;

        if ($profile?->business_name) {
            return $profile->business_name;
        }

        return $this->assignee?->name ?? 'Unassigned';
    }
}
