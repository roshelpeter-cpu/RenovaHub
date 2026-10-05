<?php

namespace App\Http\Requests\Contractor;

use App\Models\ChangeRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondChangeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $change = $this->route('changeRequest');

        return $change instanceof ChangeRequest && ($this->user()?->can('respond', $change) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cost_impact' => ['required', 'numeric', 'min:-100000000', 'max:100000000'],
            'timeline_impact' => ['required', 'string', 'max:120'],
            'feasibility' => ['required', Rule::in(['possible', 'not_possible', 'needs_design'])],
            'contractor_response' => ['required', 'string', 'max:2000'],
            'design_affected' => ['nullable', 'boolean'],
        ];
    }
}
