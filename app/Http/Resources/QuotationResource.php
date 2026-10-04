<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'number' => $this->number,
            'description' => $this->description,
            'materials' => $this->materials,
            'labour' => $this->labour,
            'additional_costs' => $this->additional_costs,
            'discount' => $this->discount,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'status' => $this->status,
            'valid_until' => $this->valid_until?->toDateString(),
            'items' => QuotationItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
