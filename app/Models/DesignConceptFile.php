<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DesignConceptFile extends Model
{
    protected $fillable = ['design_concept_id', 'kind', 'path', 'caption'];

    public function concept(): BelongsTo
    {
        return $this->belongsTo(DesignConcept::class, 'design_concept_id');
    }

    public function url(): string
    {
        return str_starts_with($this->path, 'images/')
            ? asset($this->path)
            : Storage::disk('public')->url($this->path);
    }
}
