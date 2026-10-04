<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project !== null && ($this->user()?->can('update', $project) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estimated_budget' => ['required', 'numeric', 'min:0.01'],
            'expected_start_date' => ['required', 'date'],
            'expected_completion_date' => ['required', 'date', 'after:expected_start_date'],
            'timeline_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $cleared = [];

        foreach (['estimated_budget', 'expected_start_date', 'expected_completion_date'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $cleared[$field] = null;
            }
        }

        if ($cleared !== []) {
            $this->merge($cleared);
        }
    }
}
