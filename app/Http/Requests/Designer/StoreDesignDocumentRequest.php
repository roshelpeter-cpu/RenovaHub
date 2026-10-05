<?php

namespace App\Http\Requests\Designer;

use App\Models\Document;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesignDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        if (! $project instanceof Project && $this->filled('project_id')) {
            $project = Project::query()->find($this->integer('project_id'));
        }

        return $project instanceof Project && ($this->user()?->can('uploadDesign', [\App\Models\Document::class, $project]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', Rule::in(Document::designerCategories())],
            'file' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,pdf,webp'],
        ];
    }
}
