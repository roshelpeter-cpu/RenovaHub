<?php

namespace App\Http\Requests\Contractor;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project
            && ($this->user()?->can('construct', $project) ?? false)
            && ! $project->isClosedRecord();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'spent' => ['required', 'array'],
            'spent.*' => ['integer', 'min:0', 'max:100'],
        ];
    }
}
