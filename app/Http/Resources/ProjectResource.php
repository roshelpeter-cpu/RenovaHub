<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'renovation_type' => $this->renovation_type,
            'property_type' => $this->property_type,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'estimated_budget' => $this->estimated_budget,
            'current_budget' => $this->current_budget,
            'currency' => $this->currency,
            'expected_start_date' => $this->expected_start_date?->toDateString(),
            'expected_completion_date' => $this->expected_completion_date?->toDateString(),
            'actual_completion_date' => $this->actual_completion_date?->toDateString(),
            'status' => $this->status,
            'progress' => $this->progress,
            'designer_id' => $this->designer_id,
            'contractor_id' => $this->contractor_id,
        ];
    }
}
