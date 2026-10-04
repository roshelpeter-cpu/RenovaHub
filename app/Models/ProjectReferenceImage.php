<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectReferenceImage extends Model
{
    protected $fillable = ['project_id', 'path', 'original_name'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): string
    {
        // Demo galleries reuse public/images assets. Uploaded files stay on the public disk.
        if (str_starts_with($this->path, 'images/')) {
            return asset($this->path);
        }

        return Storage::disk('public')->url($this->path);
    }
}
