<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MoodBoard extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'summary',
        'version',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MoodBoardItem::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(DesignFeedback::class);
    }
}
