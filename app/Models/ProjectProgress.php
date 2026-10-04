<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectProgress extends Model
{
    protected $table = 'project_progress';

    protected $fillable = ['project_id', 'stage', 'percent', 'started_on', 'ended_on', 'notes'];

    protected function casts(): array
    {
        return [
            'percent' => 'integer',
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }

    public function periodLabel(): ?string
    {
        if ($this->started_on && $this->ended_on) {
            return $this->started_on->format('M Y').' – '.$this->ended_on->format('M Y');
        }

        if ($this->started_on) {
            return $this->started_on->format('M Y');
        }

        return null;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function label(): string
    {
        return match ($this->stage) {
            'inspection' => 'Final Inspection',
            default => ucfirst($this->stage),
        };
    }

    public function homeStatus(): string
    {
        if ($this->percent >= 100) {
            return 'Completed';
        }

        if ($this->percent <= 0) {
            return 'Not Started';
        }

        return $this->percent.'%';
    }
}
