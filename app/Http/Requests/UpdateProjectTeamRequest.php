<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project !== null && ($this->user()?->can('update', $project) ?? false);
    }

    /**
     * Role is checked against the users table. A posted role value is ignored.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'designer_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'designer')),
            ],
            'contractor_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'contractor')),
            ],
            'complete' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $cleared = [];

        foreach (['designer_id', 'contractor_id'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $cleared[$field] = null;
            }
        }

        if ($cleared !== []) {
            $this->merge($cleared);
        }
    }
}
