<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'renovation_type' => ['required', 'string', 'max:50', Rule::in(array_keys(Project::renovationTypes()))],
            'property_type' => ['required', 'string', 'max:50', Rule::in(array_keys(Project::propertyTypes()))],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'additional_instructions' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0.01'],
            'expected_start_date' => ['nullable', 'date'],
            'expected_completion_date' => ['nullable', 'date', 'after:expected_start_date'],
            'timeline_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $cleared = [];

        foreach (['address', 'city', 'province', 'postal_code', 'estimated_budget', 'expected_start_date', 'expected_completion_date', 'timeline_notes'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $cleared[$field] = null;
            }
        }

        if ($cleared !== []) {
            $this->merge($cleared);
        }
    }
}
