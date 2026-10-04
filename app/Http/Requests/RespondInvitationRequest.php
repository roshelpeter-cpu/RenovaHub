<?php

namespace App\Http\Requests;

use App\Models\ProjectInvitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invitation = $this->route('invitation');

        return $invitation instanceof ProjectInvitation
            && ($this->user()?->can('respond', $invitation) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in([ProjectInvitation::STATUS_ACCEPTED, ProjectInvitation::STATUS_DECLINED])],
        ];
    }
}
