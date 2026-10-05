<?php

namespace App\Http\Requests\Contractor;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = Project::query()->find($this->integer('project_id'));

        return $project instanceof Project && ($this->user()?->can('create', [ProjectTask::class, $project]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'assignee_label' => ['nullable', 'string', 'max:120'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high'])],
            'started_on' => ['nullable', 'date'],
            'due_on' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['not_started', 'pending', 'in_progress', 'completed'])],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'category' => ['required', Rule::in(array_keys(ProjectTask::categories()))],
        ];
    }
}
