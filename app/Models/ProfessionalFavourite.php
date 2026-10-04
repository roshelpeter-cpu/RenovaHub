<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalFavourite extends Model
{
    protected $fillable = ['user_id', 'professional_profile_id'];

    public function homeowner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }
}
