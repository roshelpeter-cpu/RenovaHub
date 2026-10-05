<?php

namespace App\Http\Requests\Designer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDesignerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDesigner() === true;
    }

    /**
     * Role is intentionally absent. A designer cannot promote themselves.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:120'],
            'location' => ['required', 'string', 'max:120'],
            'bio' => ['required', 'string', 'max:2000'],
            'about' => ['nullable', 'string', 'max:4000'],
            'specialization' => ['required', 'string', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
