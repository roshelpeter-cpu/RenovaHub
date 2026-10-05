<?php

namespace App\Http\Requests\Contractor;

use App\Models\PaymentAllocation;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmDisbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');
        $allocation = $this->route('allocation');

        return $project instanceof Project
            && $allocation instanceof PaymentAllocation
            && (int) $allocation->project_id === (int) $project->id
            && ($this->user()?->can('pay', $allocation) ?? false);
    }

    /**
     * Status is not accepted from the browser.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
