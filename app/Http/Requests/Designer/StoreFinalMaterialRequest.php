<?php

namespace App\Http\Requests\Designer;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreFinalMaterialRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'room' => ['required', 'string', 'max:80'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:20'],
            'specification' => ['nullable', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:40'],
        ];
    }
}
