<?php

namespace App\Http\Requests\Contractor;

use App\Models\Project;
use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = Project::query()->find($this->integer('project_id'));

        return $project instanceof Project && ($this->user()?->can('create', [Quotation::class, $project]) ?? false);
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'description' => ['required', 'string', 'max:2000'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
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
