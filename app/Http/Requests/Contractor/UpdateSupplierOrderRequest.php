<?php

namespace App\Http\Requests\Contractor;

use App\Models\SupplierOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof SupplierOrder && ($this->user()?->can('update', $order) ?? false);
    }

    /**
     * Paid is absent from the allowed list. Payment status changes only
     * after the provider confirms the transaction.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $order = $this->route('order');
        $allowed = $order instanceof SupplierOrder ? $order->contractorTransitions() : [];

        return [
            'status' => ['required', Rule::in($allowed)],
        ];
    }
}
