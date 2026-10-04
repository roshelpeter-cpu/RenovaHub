<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'quotation_id' => $this->quotation_id,
            'reference' => $this->reference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toIso8601String(),
        ];
    }
}
