<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $primaryImage = $this->whenLoaded('images', fn () => $this->images->firstWhere('is_primary', true) ?? $this->images->first());

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'listing_type' => $this->listing_type?->value,
            'listing_source' => $this->listing_source?->value,
            'status' => $this->status?->value,
            'moderation_status' => $this->moderation_status?->value,
            'property_type' => $this->property_type,
            'furnishing_status' => $this->furnishing_status,
            'description' => $this->description,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'balconies' => $this->balconies,
            'parking_spaces' => $this->parking_spaces,
            'floors' => $this->floors,
            'year_built' => $this->year_built,
            'area_value' => $this->area_value,
            'area_unit' => $this->area_unit,
            'price' => $this->price,
            'security_deposit' => $this->security_deposit,
            'short_term_rate' => $this->short_term_rate,
            'currency' => $this->currency,
            'available_from' => $this->available_from?->toDateString(),
            'amenities' => [
                'has_parking' => $this->has_parking,
                'has_pool' => $this->has_pool,
                'has_air_conditioning' => $this->has_air_conditioning,
                'is_furnished' => $this->is_furnished,
                'has_gym' => $this->has_gym,
                'has_security' => $this->has_security,
                'pets_allowed' => $this->pets_allowed,
            ],
            'location' => [
                'address_line_1' => $this->address_line_1,
                'address_line_2' => $this->address_line_2,
                'locality' => $this->locality,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
                'full_address' => $this->full_address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'views_count' => $this->views_count,
            'moderation_notes' => $this->moderation_notes,
            'rejection_reason' => $this->rejection_reason,
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'images' => $this->whenLoaded('images', fn () => PropertyImageResource::collection($this->images)),
            'primary_image' => $this->when(
                $this->relationLoaded('images') && $primaryImage !== null,
                fn () => new PropertyImageResource($primaryImage)
            ),
            'user' => $this->whenLoaded('user', fn () => new UserSummaryResource($this->user)),
            'company' => $this->whenLoaded('company', fn () => $this->company ? new CompanyResource($this->company) : null),
            'reviewer' => $this->whenLoaded('reviewer', fn () => $this->reviewer ? new UserSummaryResource($this->reviewer) : null),
            'inquiries' => $this->whenLoaded('inquiries', fn () => InquiryResource::collection($this->inquiries)),
            'leads' => $this->whenLoaded('leads', fn () => LeadResource::collection($this->leads)),
            'reports_count' => $this->when(isset($this->reports_count), $this->reports_count),
        ];
    }
}
