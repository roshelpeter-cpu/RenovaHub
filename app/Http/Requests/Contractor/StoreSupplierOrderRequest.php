<?php

namespace App\Http\Requests\Contractor;

use App\Models\SupplierPrice;
use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $price = $this->route('price');

        return $price instanceof SupplierPrice && ($this->user()?->can('view', $price) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'delivery_address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
