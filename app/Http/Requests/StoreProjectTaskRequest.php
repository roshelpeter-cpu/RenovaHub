<?php

namespace App\Http\Requests;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectTaskRequest extends FormRequest
{
    /**
     * Homeowners fail this check. Only the project's contractor can create a task.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project
            && ($this->user()?->can('create', [ProjectTask::class, $project]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $project = $this->route('project');
        $allowed = $project instanceof Project
            ? array_filter([$project->user_id, $project->designer_id, $project->contractor_id])
            : [];

        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', Rule::in(array_keys(ProjectTask::categories()))],
            'assignee_id' => ['nullable', 'integer', Rule::in($allowed)],
            'started_on' => ['nullable', 'date'],
            'due_on' => ['nullable', 'date'],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high'])],
        ];
    }
}
