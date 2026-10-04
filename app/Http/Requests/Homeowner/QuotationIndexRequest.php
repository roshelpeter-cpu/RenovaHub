<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuotationIndexRequest extends FormRequest
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
            'status' => ['nullable', Rule::in([
                Quotation::STATUS_PENDING,
                Quotation::STATUS_APPROVED,
                Quotation::STATUS_REJECTED,
                Quotation::STATUS_CLARIFICATION,
                Quotation::STATUS_EXPIRED,
            ])],
            'professional' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['project', 'status', 'professional', 'search', 'from', 'to'] as $field) {
            if (! $this->filled($field)) {
                $this->merge([$field => null]);
            }
        }

        if ($this->filled('search')) {
            $this->merge(['search' => trim((string) $this->input('search'))]);
        }
    }
}
