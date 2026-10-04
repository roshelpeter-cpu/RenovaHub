<?php

namespace App\Http\Requests\Homeowner;

use App\Models\Conversation;
use Illuminate\Foundation\Http\FormRequest;

class StoreConversationMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $conversation = $this->route('conversation');

        return $this->user()?->isHomeowner() === true
            && $conversation instanceof Conversation
            && $this->user()->can('update', $conversation);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:4000'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'max:5120'],
        ];
    }
}
