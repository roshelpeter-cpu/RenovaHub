<?php

namespace App\Http\Requests\Homeowner;

use Illuminate\Foundation\Http\FormRequest;

class ConsultationRequest extends FormRequest
{
    /**
     * Consultation requests are stored as a flash confirmation only in this
     * stage. Validation still lives here so Blade never trusts raw input.
     */
    public function authorize(): bool
    {
        return $this->user()?->isHomeowner() === true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
