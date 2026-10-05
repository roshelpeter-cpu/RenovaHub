<?php

namespace App\Http\Requests\Contractor;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreConstructionQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = Project::query()->find($this->integer('project_id'));

        return $project !== null && $this->user()?->can('construct', $project);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:2000'],
            'start_date' => ['required', 'date'],
            'completion_date' => ['required', 'date', 'after:start_date'],
            'scope' => ['required', 'string', 'max:5000'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'warranty' => ['nullable', 'string', 'max:255'],
        ];
    }
}
