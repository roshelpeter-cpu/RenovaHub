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
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'estimated_budget',
        'current_budget',
        'currency',
        'expected_start_date',
        'expected_completion_date',
        'actual_completion_date',
        'timeline_notes',
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
            'expected_start_date' => 'date',
            'expected_completion_date' => 'date',
            'actual_completion_date' => 'date',
            'progress' => 'integer',
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

    public function activity(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function referenceImages(): HasMany
    {
        return $this->hasMany(ProjectReferenceImage::class);
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
     * @return list<string>
     */
    public static function progressStages(): array
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

    public function propertyTypeLabel(): string
    {
        return self::propertyTypes()[$this->property_type] ?? $this->property_type;
    }

    public function locationLabel(): ?string
    {
        $parts = array_values(array_filter([$this->city, $this->province]));

        if ($parts !== []) {
            return implode(', ', $parts);
        }

        return $this->address;
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

        return $stages->sortBy(fn (ProjectProgress $stage) => array_search($stage->stage, self::progressStages(), true));
    }
}
