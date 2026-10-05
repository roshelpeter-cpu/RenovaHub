<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreDesignChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && ($this->user()?->can('create', [\App\Models\DesignChangeRequest::class, $project]) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:4000'],
            'design_impact' => ['nullable', 'string', 'max:255'],
        ];
    }
}
