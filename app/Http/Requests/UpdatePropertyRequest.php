<?php

namespace App\Http\Requests;

use App\Enums\PropertyListingType;
use App\Enums\PropertyStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'listing_type' => ['required', Rule::in(PropertyListingType::values())],
            'status' => ['required', Rule::in(PropertyStatus::values())],
            'property_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'furnishing_status' => ['nullable', 'string', 'max:100'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'balconies' => ['nullable', 'integer', 'min:0', 'max:50'],
            'parking_spaces' => ['nullable', 'integer', 'min:0', 'max:50'],
            'floors' => ['nullable', 'integer', 'min:0', 'max:200'],
            'year_built' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'area_value' => ['nullable', 'numeric', 'min:0'],
            'area_unit' => ['nullable', 'string', 'max:20'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['nullable', 'numeric', 'min:0'],
            'short_term_rate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'available_from' => ['nullable', 'date'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:120'],
            'locality' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'has_parking' => ['nullable', 'boolean'],
            'has_pool' => ['nullable', 'boolean'],
            'has_air_conditioning' => ['nullable', 'boolean'],
            'is_furnished' => ['nullable', 'boolean'],
            'has_gym' => ['nullable', 'boolean'],
            'has_security' => ['nullable', 'boolean'],
            'pets_allowed' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:8192'],
            'gallery_manifest' => ['required', 'json'],
            'crop_payloads' => ['nullable', 'json'],
            'primary_media_key' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => strip_tags((string) $this->input('description')),
            'has_parking' => $this->boolean('has_parking'),
            'has_pool' => $this->boolean('has_pool'),
            'has_air_conditioning' => $this->boolean('has_air_conditioning'),
            'is_furnished' => $this->boolean('is_furnished'),
            'has_gym' => $this->boolean('has_gym'),
            'has_security' => $this->boolean('has_security'),
            'pets_allowed' => $this->boolean('pets_allowed'),
        ]);
    }
}
