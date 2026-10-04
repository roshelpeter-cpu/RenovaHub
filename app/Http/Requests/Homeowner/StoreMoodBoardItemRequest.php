<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMoodBoardItemRequest extends FormRequest
{
    /**
     * Inspiration can be added only while the selected project is still open.
     */
    public function authorize(): bool
    {
        $project = $this->project();

        return $project !== null && ($this->user()?->can('contribute', $project) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'min:1'],
            'kind' => ['required', Rule::in(['inspiration', 'colour', 'material', 'furniture', 'note'])],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:1000'],
            'colour' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function project(): ?Project
    {
        $routed = $this->route('project');

        if ($routed instanceof Project) {
            return $routed;
        }

        $id = (int) $this->input('project_id');

        return $id > 0 ? $this->user()?->projects()->find($id) : null;
    }
}
