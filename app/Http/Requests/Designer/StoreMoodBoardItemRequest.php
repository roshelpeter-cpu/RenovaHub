<?php

namespace App\Http\Requests\Designer;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMoodBoardItemRequest extends FormRequest
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
            'kind' => ['required', Rule::in(['inspiration', 'colour', 'material', 'furniture', 'note'])],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:2000'],
            'colour' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
