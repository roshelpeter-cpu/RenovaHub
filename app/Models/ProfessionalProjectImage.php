<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalProjectImage extends Model
{
    protected $fillable = ['professional_project_id', 'path', 'caption', 'image_type', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProject::class, 'professional_project_id');
    }

    public function url(): string
    {
        return asset($this->path);
    }
}
