<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Project extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PLANNING = 'planning';

    public const STATUS_AWAITING_TEAM = 'awaiting_team';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_ARCHIVED = 'archived';

    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * user_id is intentionally absent so a request cannot assign ownership.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'requirements',
        'additional_instructions',
        'renovation_type',
        'property_type',
        'size_sq_ft',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'estimated_budget',
        'current_budget',
        'final_cost',
        'currency',
        'expected_start_date',
        'expected_completion_date',
        'actual_completion_date',
        'timeline_notes',
        'workspace_meta',
        'status',
        'progress',
        'cover_image',
        'designer_id',
        'contractor_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'estimated_budget' => 'decimal:2',
            'current_budget' => 'decimal:2',
            'final_cost' => 'decimal:2',
            'expected_start_date' => 'date',
            'expected_completion_date' => 'date',
            'actual_completion_date' => 'date',
            'progress' => 'integer',
            'size_sq_ft' => 'integer',
            'workspace_meta' => 'array',
            'designer_id' => 'integer',
            'contractor_id' => 'integer',
        ];
    }

    public function homeowner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(ProjectTeamMember::class);
    }

    public function progressStages(): HasMany
    {
        return $this->hasMany(ProjectProgress::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function moodBoard(): HasOne
    {
        return $this->hasOne(MoodBoard::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(ProjectFeedback::class);
    }

    public function activity(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function referenceImages(): HasMany
    {
        return $this->hasMany(ProjectReferenceImage::class);
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(ProjectBudgetItem::class)->orderBy('sort_order');
    }

    /**
     * Gallery alias so controllers can eager-load images without a second table.
     * Rows still live on project_reference_images.
     */
    public function images(): HasMany
    {
        return $this->referenceImages();
    }

    /**
     * My Projects cards show four workflow stages, not the procurement
     * row that still exists for older task records.
     *
     * @return Collection<int, object{key: string, label: string, percent: int, status: string, dates: ?string, current: bool}>
     */
    public function workspaceStages(): Collection
    {
        $stored = $this->orderedProgress()->keyBy('stage');

        return collect([
            'design' => 'Design',
            'planning' => 'Planning',
            'construction' => 'Construction',
            'inspection' => 'Final Inspection',
        ])->map(function (string $label, string $key) use ($stored) {
            $row = $stored->get($key);
            $percent = (int) ($row?->percent ?? 0);
            $current = $percent > 0 && $percent < 100;

            return (object) [
                'key' => $key,
                'label' => $label,
                'percent' => $percent,
                'current' => $current,
                'dates' => $row instanceof ProjectProgress ? $row->periodLabel() : null,
                'notes' => $row instanceof ProjectProgress ? $row->notes : null,
                // Card copy stays "In Progress — 68%". The detail timeline uses state instead.
                'status' => $percent >= 100 ? 'Completed' : ($percent <= 0 ? 'Not Started' : 'In Progress — '.$percent.'%'),
                'state' => $percent >= 100 ? 'completed' : ($percent <= 0 ? 'upcoming' : 'current'),
            ];
        })->values();
    }

    public function currentStageLabel(): string
    {
        $current = $this->workspaceStages()->first(fn ($stage) => $stage->current);

        if ($current) {
            return $current->label;
        }

        return $this->status === self::STATUS_COMPLETED ? 'Completed' : 'Planning';
    }

    public function clientReview(): ?object
    {
        $review = $this->workspace_meta['review'] ?? null;

        if (! is_array($review) || empty($review['body'])) {
            return null;
        }

        return (object) [
            'name' => $review['name'] ?? 'Client',
            'rating' => (float) ($review['rating'] ?? 5),
            'body' => $review['body'],
        ];
    }

    /**
     * @return Collection<int, ProjectReferenceImage>
     */
    public function gallery(): Collection
    {
        $images = $this->relationLoaded('referenceImages') ? $this->referenceImages : $this->referenceImages()->get();

        return $images->values();
    }

    /**
     * @return array<string, string>
     */
    public static function renovationTypes(): array
    {
        return [
            'full_house' => 'Full House',
            'kitchen' => 'Kitchen',
            'bathroom' => 'Bathroom',
            'living_room' => 'Living Room',
            'outdoor' => 'Outdoor',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function propertyTypes(): array
    {
        return [
            'house' => 'House',
            'apartment' => 'Apartment',
            'villa' => 'Villa',
            'condominium' => 'Condominium',
            'commercial' => 'Commercial',
            'other' => 'Other',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PLANNING => 'Planning',
            self::STATUS_AWAITING_TEAM => 'Awaiting Team',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Display order for the five progress stages.
     * This is separate from the progressStages() relationship.
     *
     * @return list<string>
     */
    public static function stageOrder(): array
    {
        return ['design', 'planning', 'procurement', 'construction', 'inspection'];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? 'Planning';
    }

    public function renovationTypeLabel(): string
    {
        return self::renovationTypes()[$this->renovation_type] ?? $this->renovation_type;
    }

    /**
     * Catalogue copy can be more specific than the stored renovation enum
     * without changing how new projects are created.
     */
    public function displayTypeLabel(): string
    {
        $meta = $this->workspace_meta ?? [];

        return $meta['type_label'] ?? $this->renovationTypeLabel();
    }

    /**
     * My Projects cards follow "City, Sri Lanka" from the reference.
     * Home still uses locationLabel() so its approved copy is untouched.
     */
    public function cataloguePlaceLabel(): string
    {
        return $this->city ? $this->city.', Sri Lanka' : ($this->locationLabel() ?: 'Location not added yet');
    }

    public function propertyTypeLabel(): string
    {
        return self::propertyTypes()[$this->property_type] ?? $this->property_type;
    }

    public function locationLabel(): ?string
    {
        $province = $this->province;

        if ($province && ! str_contains(strtolower($province), 'province')) {
            $province .= ' Province';
        }

        $parts = array_values(array_filter([$this->city, $province]));

        if ($parts !== []) {
            return implode(', ', $parts);
        }

        return $this->address;
    }

    /**
     * Compact LKR label for summary tiles. Exact amounts stay on the project cards.
     */
    public static function compactMoney(float $amount): string
    {
        if ($amount >= 1000000) {
            return 'LKR '.number_format($amount / 1000000, 1).'M';
        }

        return 'LKR '.number_format($amount, 0);
    }

    public function expectedDurationLabel(): ?string
    {
        if ($this->expected_start_date === null || $this->expected_completion_date === null) {
            return null;
        }

        $days = $this->expected_start_date->diffInDays($this->expected_completion_date);

        return $days.' days';
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset($this->cover_image) : null;
    }

    /**
     * Money stays on the model so Blade only prints the formatted result.
     */
    public function money(?string $amount): string
    {
        if ($amount === null || $amount === '') {
            return 'Not set';
        }

        return ($this->currency ?: 'LKR').' '.number_format((float) $amount, 2);
    }

    public function paidAmount(): float
    {
        $payments = $this->relationLoaded('payments') ? $this->payments : $this->payments()->get();

        return (float) $payments->where('status', Payment::STATUS_PAID)->sum(fn (Payment $payment) => (float) $payment->amount);
    }

    public function approvedQuotationTotal(): float
    {
        $quotations = $this->relationLoaded('quotations') ? $this->quotations : $this->quotations()->get();

        return (float) $quotations->where('status', Quotation::STATUS_APPROVED)->sum(fn (Quotation $quotation) => (float) $quotation->total);
    }

    public function outstandingAmount(): float
    {
        $base = $this->approvedQuotationTotal();

        if ($base <= 0 && $this->current_budget !== null) {
            $base = (float) $this->current_budget;
        }

        return max(0, $base - $this->paidAmount());
    }

    public function latestInvitation(string $role): ?ProjectInvitation
    {
        $invitations = $this->relationLoaded('invitations') ? $this->invitations : $this->invitations()->get();

        return $invitations->where('role', $role)->sortByDesc('id')->first();
    }

    /**
     * @return Collection<int, ProjectProgress>
     */
    public function orderedProgress(): Collection
    {
        $stages = $this->relationLoaded('progressStages') ? $this->progressStages : $this->progressStages()->get();

        return $stages->sortBy(fn (ProjectProgress $stage) => array_search($stage->stage, self::stageOrder(), true));
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Completed and archived projects stay readable as history.
     * New documents, inspiration, change requests and tasks stop here.
     */
    public function isClosedRecord(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_ARCHIVED], true);
    }

    /**
     * Jan through Jun is six months on the project cards, counting both ends.
     */
    public function durationMonths(): ?int
    {
        $start = $this->expected_start_date;
        $end = $this->scheduleEnd();

        if ($start === null || $end === null) {
            return null;
        }

        return (($end->year - $start->year) * 12) + ($end->month - $start->month) + 1;
    }

    public function durationLabel(): ?string
    {
        $months = $this->durationMonths();

        if ($months === null) {
            return null;
        }

        return $months.' '.($months === 1 ? 'Month' : 'Months');
    }

    public function durationRangeLabel(): ?string
    {
        $start = $this->expected_start_date;
        $end = $this->scheduleEnd();

        if ($start === null || $end === null) {
            return null;
        }

        return $start->format('M Y').' – '.$end->format('M Y');
    }

    public function scheduleEnd(): ?\Illuminate\Support\Carbon
    {
        if ($this->isCompleted()) {
            return $this->actual_completion_date ?? $this->expected_completion_date;
        }

        return $this->expected_completion_date;
    }

    public function approvedBudgetAmount(): float
    {
        return (float) ($this->current_budget ?? $this->estimated_budget ?? 0);
    }

    /**
     * Completed summary uses the stored final cost when the category lines
     * were agreed before the last variations.
     */
    public function finalCostAmount(): float
    {
        if ($this->final_cost !== null) {
            return (float) $this->final_cost;
        }

        $items = $this->relationLoaded('budgetItems') ? $this->budgetItems : $this->budgetItems()->get();
        $sum = (float) $items->sum(fn (ProjectBudgetItem $item) => (float) $item->amount);

        return $sum > 0 ? $sum : $this->approvedBudgetAmount();
    }

    public function budgetTotalAmount(): float
    {
        return (float) ($this->estimated_budget ?? 0);
    }

    public function amountSpent(): float
    {
        $items = $this->relationLoaded('budgetItems') ? $this->budgetItems : $this->budgetItems()->get();

        return (float) $items->sum(fn (ProjectBudgetItem $item) => $item->spentAmount());
    }

    public function amountRemaining(): float
    {
        return max(0, $this->budgetTotalAmount() - $this->amountSpent());
    }

    public function spentPercent(): int
    {
        $total = $this->budgetTotalAmount();

        if ($total <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->amountSpent() / $total) * 100));
    }

    public function metaString(string $key, ?string $fallback = null): ?string
    {
        $value = ($this->workspace_meta ?? [])[$key] ?? null;

        return is_scalar($value) && (string) $value !== '' ? (string) $value : $fallback;
    }

    public function progressCaption(): string
    {
        if ($this->isCompleted() || (int) $this->progress >= 100) {
            return 'Completed successfully';
        }

        return $this->metaString('progress_note', 'On track') ?? 'On track';
    }

    /**
     * Tab badges read counts once. Calling this from the partial avoids
     * repeating the same four queries on every project section.
     */
    public function ensureSectionCounts(): void
    {
        if (! array_key_exists('tasks_count', $this->attributes)) {
            $this->loadCount(['tasks', 'documents', 'quotations', 'changeRequests']);
        }
    }
}
