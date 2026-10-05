<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class RequestFinalDesignChangesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && $this->user()?->can('contribute', $project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'comment' => ['required', 'string', 'min:8', 'max:2000'],
        ];
    }
}
