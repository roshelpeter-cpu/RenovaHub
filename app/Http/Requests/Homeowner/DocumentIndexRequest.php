<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentIndexRequest extends FormRequest
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
            'type' => ['nullable', Rule::in(array_keys(Document::categories()))],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project' => $this->filled('project') ? $this->input('project') : null,
            'type' => $this->filled('type') ? $this->input('type') : null,
            'search' => $this->filled('search') ? trim((string) $this->input('search')) : null,
        ]);
    }
}
