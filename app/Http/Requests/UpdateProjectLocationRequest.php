<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectLocationRequest extends FormRequest
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
        return StoreProjectRequest::locationRules();
    }

    protected function prepareForValidation(): void
    {
        $cleared = [];

        foreach (['address', 'city', 'province', 'postal_code', 'latitude', 'longitude'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $cleared[$field] = null;
            }
        }

        if ($cleared !== []) {
            $this->merge($cleared);
        }
    }
}
