<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'name',
        'original_name',
        'category',
        'disk',
        'path',
        'mime',
        'size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sizeLabel(): string
    {
        $size = (int) $this->size;

        if ($size < 1024) {
            return $size.' B';
        }

        if ($size < 1048576) {
            return round($size / 1024).' KB';
        }

        return number_format($size / 1048576, 1).' MB';
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
