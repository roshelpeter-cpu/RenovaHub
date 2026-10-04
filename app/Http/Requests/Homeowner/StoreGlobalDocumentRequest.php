<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGlobalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $projectId = (int) $this->input('project_id');

        if ($user === null || ! $user->isHomeowner() || $projectId < 1) {
            return false;
        }

        $project = $user->projects()->find($projectId);

        return $project !== null && $user->can('contribute', $project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['required', Rule::in(array_keys(Document::categories()))],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:10240'],
        ];
    }
}
