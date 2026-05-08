<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
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
            'property_id' => $this->property_id,
            'recipient_user_id' => $this->recipient_user_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'preferred_contact_method' => $this->preferred_contact_method?->value,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'property' => $this->whenLoaded('property', fn () => [
                'id' => $this->property->id,
                'slug' => $this->property->slug,
                'title' => $this->property->title,
            ]),
            'recipient' => $this->whenLoaded('recipient', fn () => new UserSummaryResource($this->recipient)),
        ];
    }
}
