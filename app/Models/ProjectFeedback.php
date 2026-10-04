<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFeedback extends Model
{
    protected $table = 'project_feedback';

    protected $fillable = [
        'project_id',
        'homeowner_id',
        'professional_id',
        'role',
        'rating',
        'title',
        'comment',
        'attachment',
    ];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function homeowner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeowner_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
