<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgress extends Model
{
    protected $table = 'project_progress';

    protected $fillable = ['project_id', 'stage', 'percent'];

    protected function casts(): array
    {
        return ['percent' => 'integer'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function label(): string
    {
        return ucfirst($this->stage);
    }
}
