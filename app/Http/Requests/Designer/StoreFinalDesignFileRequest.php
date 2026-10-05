<?php

namespace App\Http\Requests\Designer;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinalDesignFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && $this->user()?->can('design', $project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'kind' => ['required', Rule::in(['render', 'floor_plan', 'visualisation'])],
            'caption' => ['nullable', 'string', 'max:160'],
            'image' => ['required', 'image', 'max:8192'],
        ];
    }
}
