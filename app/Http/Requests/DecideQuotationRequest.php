<?php

namespace App\Http\Requests;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecideQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $quotation = $this->route('quotation');

        return $quotation instanceof Quotation && ($this->user()?->can('update', $quotation) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in([
                Quotation::STATUS_APPROVED,
                Quotation::STATUS_REJECTED,
                Quotation::STATUS_CLARIFICATION,
            ])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
