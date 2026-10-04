<?php

namespace App\Http\Requests\Homeowner;

use App\Models\ProjectTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHomeowner() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', Rule::in(array_keys(ProjectTask::filterStatuses()))],
            'category' => ['nullable', Rule::in(array_keys(ProjectTask::categories()))],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project' => $this->filled('project') ? $this->input('project') : null,
            'status' => $this->filled('status') ? $this->input('status') : null,
            'category' => $this->filled('category') ? $this->input('category') : null,
            'search' => $this->filled('search') ? trim((string) $this->input('search')) : null,
        ]);
    }
}
