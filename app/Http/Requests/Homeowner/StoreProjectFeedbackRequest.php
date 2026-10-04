<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && ($this->user()?->can('create', [\App\Models\ProjectFeedback::class, $project]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(['designer', 'contractor'])],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:150'],
            'comment' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ];
    }
}
