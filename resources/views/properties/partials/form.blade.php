@php
    $existingImages = $property->relationLoaded('images')
        ? $property->images->map(fn ($image) => [
            'type' => 'existing',
            'key' => 'existing-'.$image->id,
            'id' => $image->id,
            'preview' => Storage::url($image->thumbnail_path),
            'name' => $image->alt_text ?: 'Property image',
            'is_primary' => $image->is_primary,
        ])->values()
        : collect();
@endphp

<div class="space-y-8" data-property-form data-existing-images='@json($existingImages)'>
    <input type="hidden" name="gallery_manifest" value="{{ old('gallery_manifest', $existingImages->toJson()) }}" data-gallery-manifest>
    <input type="hidden" name="crop_payloads" value="{{ old('crop_payloads', '{}') }}" data-crop-payloads>
    <input type="hidden" name="primary_media_key" value="{{ old('primary_media_key', $existingImages->firstWhere('is_primary', true)['key'] ?? '') }}" data-primary-media-key>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Listing basics</h3>
            <p class="mt-1 text-sm text-slate-500">Add the core details buyers, tenants, and guests need first.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <div class="md:col-span-2">
                <x-input-label for="title" value="Property Title" />
                <x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $property->title)" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="listing_type" value="Listing Type" />
                <select id="listing_type" name="listing_type" class="mt-1 block w-full rounded-2xl border-slate-300">
                    @foreach (\App\Enums\PropertyListingType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(old('listing_type', $property->listing_type?->value) === $type->value)>{{ str($type->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="status" value="Listing Status" />
                <select id="status" name="status" class="mt-1 block w-full rounded-2xl border-slate-300">
                    @foreach (\App\Enums\PropertyStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $property->status?->value ?? \App\Enums\PropertyStatus::Draft->value) === $status->value)>{{ str($status->value)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="property_type" value="Property Type" />
                <x-text-input id="property_type" name="property_type" class="mt-1 block w-full" :value="old('property_type', $property->property_type)" required />
            </div>

            <div>
                <x-input-label for="furnishing_status" value="Furnishing Status" />
                <x-text-input id="furnishing_status" name="furnishing_status" class="mt-1 block w-full" :value="old('furnishing_status', $property->furnishing_status)" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-2xl border-slate-300">{{ old('description', $property->description) }}</textarea>
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Photos</h3>
            <p class="mt-1 text-sm text-slate-500">Upload 5 to 20 images. Drag to reorder, choose the thumbnail image, and crop new uploads before saving.</p>
        </div>

        <div class="rounded-3xl border-2 border-dashed border-amber-300 bg-amber-50/60 p-6 text-center transition" data-drop-zone>
            <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="hidden" data-image-input>
            <p class="text-sm font-semibold text-slate-900">Drop images here or click to upload</p>
            <p class="mt-2 text-sm text-slate-500">Supported formats: JPG, PNG, WebP. Minimum 5 images, maximum 20.</p>
            <button type="button" class="mt-4 rounded-full bg-slate-900 px-5 py-2 text-sm font-semibold text-white" data-trigger-upload>Select images</button>
        </div>
        <x-input-error :messages="$errors->get('images')" class="mt-3" />
        <x-input-error :messages="$errors->get('images.*')" class="mt-3" />

        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" data-gallery-preview></div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Pricing and specs</h3>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            @foreach ([
                ['price', 'Price'],
                ['security_deposit', 'Security Deposit'],
                ['short_term_rate', 'Short-Term Rate'],
                ['bedrooms', 'Bedrooms'],
                ['bathrooms', 'Bathrooms'],
                ['balconies', 'Balconies'],
                ['parking_spaces', 'Parking Spaces'],
                ['floors', 'Floors'],
                ['year_built', 'Year Built'],
                ['area_value', 'Area'],
            ] as [$field, $label])
                <div>
                    <x-input-label :for="$field" :value="$label" />
                    <x-text-input :id="$field" :name="$field" class="mt-1 block w-full" :value="old($field, $property->{$field})" />
                </div>
            @endforeach

            <div>
                <x-input-label for="area_unit" value="Area Unit" />
                <x-text-input id="area_unit" name="area_unit" class="mt-1 block w-full" :value="old('area_unit', $property->area_unit ?: 'sq_ft')" />
            </div>

            <div>
                <x-input-label for="currency" value="Currency" />
                <x-text-input id="currency" name="currency" class="mt-1 block w-full" :value="old('currency', $property->currency ?: 'INR')" />
            </div>

            <div>
                <x-input-label for="available_from" value="Available From" />
                <x-text-input id="available_from" name="available_from" type="date" class="mt-1 block w-full" :value="old('available_from', optional($property->available_from)->format('Y-m-d'))" />
            </div>
        </div>
    </section>

    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Location and amenities</h3>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ([
                ['address_line_1', 'Address Line 1'],
                ['address_line_2', 'Address Line 2'],
                ['city', 'City'],
                ['state', 'State'],
                ['postal_code', 'Postal Code'],
                ['country', 'Country'],
                ['locality', 'Neighborhood / Locality'],
                ['latitude', 'Latitude'],
                ['longitude', 'Longitude'],
            ] as [$field, $label])
                <div class="{{ in_array($field, ['address_line_1', 'address_line_2'], true) ? 'md:col-span-2' : '' }}">
                    <x-input-label :for="$field" :value="$label" />
                    <x-text-input :id="$field" :name="$field" class="mt-1 block w-full" :value="old($field, $property->{$field})" />
                </div>
            @endforeach
        </div>

        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                'has_parking' => 'Parking',
                'has_pool' => 'Pool',
                'has_air_conditioning' => 'Air Conditioning',
                'is_furnished' => 'Furnished',
                'has_gym' => 'Gym',
                'has_security' => 'Security',
                'pets_allowed' => 'Pets Allowed',
            ] as $field => $label)
                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700">
                    <input type="checkbox" name="{{ $field }}" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500" @checked(old($field, $property->{$field}))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </section>
</div>
