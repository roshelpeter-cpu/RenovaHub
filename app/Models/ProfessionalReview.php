<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalReview extends Model
{
    protected $fillable = [
        'professional_profile_id',
        'author_name',
        'project_title',
        'body',
        'rating',
    ];

    protected function casts(): array
    {
        return ['rating' => 'decimal:1'];
    }

    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }
}
