<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project !== null && ($this->user()?->can('contribute', $project) ?? false);
    }

    /**
     * Executables are rejected. Documents stay on the private disk.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'category' => ['required', Rule::in(array_keys(Document::categories()))],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:10240'],
        ];
    }
}
