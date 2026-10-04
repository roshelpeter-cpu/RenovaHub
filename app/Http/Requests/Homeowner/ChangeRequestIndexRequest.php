<?php

namespace App\Http\Requests\Homeowner;

use App\Models\ChangeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRequestIndexRequest extends FormRequest
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
            'status' => ['nullable', Rule::in(array_merge(
                ['pending', 'in_review', 'approved', 'rejected'],
                [
                    ChangeRequest::STATUS_SUBMITTED,
                    ChangeRequest::STATUS_UNDER_REVIEW,
                    ChangeRequest::STATUS_COST_PROVIDED,
                    ChangeRequest::STATUS_AWAITING_APPROVAL,
                    ChangeRequest::STATUS_APPROVED,
                    ChangeRequest::STATUS_REJECTED,
                    ChangeRequest::STATUS_IMPLEMENTED,
                ],
            ))],
            'category' => ['nullable', Rule::in(array_keys(ChangeRequest::categories()))],
            'search' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['project', 'status', 'category', 'search', 'from', 'to'] as $field) {
            if (! $this->filled($field)) {
                $this->merge([$field => null]);
            }
        }

        if ($this->filled('search')) {
            $this->merge(['search' => trim((string) $this->input('search'))]);
        }
    }
}
