<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'registration_number' => $this->registration_number,
            'license_number' => $this->license_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'display_location' => $this->display_location,
            'verification_status' => $this->verification_status?->value,
            'verified_at' => $this->verified_at?->toISOString(),
        ];
    }
}
