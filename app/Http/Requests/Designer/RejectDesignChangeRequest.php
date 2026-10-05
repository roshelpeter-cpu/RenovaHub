<?php

namespace App\Http\Requests\Designer;

use App\Models\DesignChangeRequest;
use Illuminate\Foundation\Http\FormRequest;

class RejectDesignChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $revision = $this->route('revision');

        return $revision instanceof DesignChangeRequest && $this->user()?->can('decide', $revision);
    }

    /**
     * A rejection without a reason would leave the homeowner without an explanation.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
