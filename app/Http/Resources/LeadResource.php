<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'inquiry_id' => $this->inquiry_id,
            'broker_user_id' => $this->broker_user_id,
            'assigned_to_user_id' => $this->assigned_to_user_id,
            'status' => $this->status?->value,
            'is_converted' => $this->is_converted,
            'converted_at' => $this->converted_at?->toISOString(),
            'booking_reference' => $this->booking_reference,
            'last_contacted_at' => $this->last_contacted_at?->toISOString(),
            'property' => $this->whenLoaded('property', fn () => [
                'id' => $this->property->id,
                'slug' => $this->property->slug,
                'title' => $this->property->title,
                'price' => $this->property->price,
                'city' => $this->property->city,
                'images' => PropertyImageResource::collection($this->property->images),
            ]),
            'inquiry' => $this->whenLoaded('inquiry', fn () => new InquiryResource($this->inquiry)),
            'broker' => $this->whenLoaded('broker', fn () => new UserSummaryResource($this->broker)),
            'assignee' => $this->whenLoaded('assignee', fn () => new UserSummaryResource($this->assignee)),
            'notes' => $this->whenLoaded('notes', fn () => $this->notes->map(fn ($note) => [
                'id' => $note->id,
                'note' => $note->note,
                'created_at' => $note->created_at?->toISOString(),
                'user' => $note->relationLoaded('user') ? new UserSummaryResource($note->user) : null,
            ])->values()),
        ];
    }
}
