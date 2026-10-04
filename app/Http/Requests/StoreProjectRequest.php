<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:5000'],
            'renovation_type' => ['required', 'string', 'max:50', Rule::in(array_keys(Project::renovationTypes()))],
            'property_type' => ['required', 'string', 'max:50', Rule::in(array_keys(Project::propertyTypes()))],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'additional_instructions' => ['nullable', 'string', 'max:5000'],
            'reference_images' => ['nullable', 'array', 'max:6'],
            'reference_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ...self::locationRules(),
        ];
    }

    /**
     * Location rules live here so the location request does not repeat them.
     *
     * @return array<string, mixed>
     */
    public static function locationRules(): array
    {
        return [
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
