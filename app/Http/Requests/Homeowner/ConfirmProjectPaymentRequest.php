<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmProjectPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');
        $payment = $this->route('payment');
        $user = $this->user();

        return $user?->isHomeowner()
            && $project instanceof Project
            && $payment instanceof Payment
            && (int) $payment->project_id === (int) $project->id
            && (int) $project->user_id === (int) $user->id
            && in_array($payment->status, [Payment::STATUS_PENDING, Payment::STATUS_PAID], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
        ];
    }
}
