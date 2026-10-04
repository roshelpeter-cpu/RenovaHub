<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'category' => $this->category,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_on' => $this->due_on?->toDateString(),
            'progress' => $this->progress,
            'assignee' => $this->assignee?->name,
        ];
    }
}
