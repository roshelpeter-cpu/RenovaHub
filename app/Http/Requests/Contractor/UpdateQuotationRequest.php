<?php

namespace App\Http\Requests\Contractor;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $quotation = $this->route('quotation');

        return $quotation instanceof Quotation && ($this->user()?->can('prepare', $quotation) ?? false);
    }

    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($row) => is_array($row) && filled($row['item'] ?? null))
            ->values()
            ->all();

        $this->merge(['items' => $items]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:2000'],
            'valid_until' => ['nullable', 'date'],
            'items' => ['required', 'array', 'min:1', 'max:30'],
            'items.*.item' => ['required', 'string', 'max:150'],
            'items.*.category' => ['required', Rule::in(['materials', 'labour', 'equipment', 'other'])],
            'items.*.description' => ['nullable', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'submit' => ['nullable', 'boolean'],
        ];
    }
}
