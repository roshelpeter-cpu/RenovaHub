<?php

namespace App\Http\Requests\Contractor;

use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $supplier = $this->route('supplier');
        $project = Project::query()->find($this->integer('project_id'));

        return $supplier instanceof Supplier
            && $project instanceof Project
            && ($this->user()?->can('construct', $project) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'material' => ['required', 'string', 'max:120'],
            'product' => ['required', 'string', 'max:150'],
            'quantity' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'unit' => ['required', 'string', 'max:40'],
            'required_by' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
