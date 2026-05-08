<?php

namespace App\Http\Controllers;

use App\Enums\PropertyListingSource;
use App\Enums\PropertyModerationStatus;
use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Models\User;
use App\Services\PropertyImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);
        $properties = Property::query()
            ->with(['images', 'user', 'company'])
            ->publiclyVisible()
            ->applyFilters($filters)
            ->applySort($request->string('sort')->toString())
            ->paginate(9)
            ->withQueryString();

        $this->recordRecentSearch($request, $filters);

        return view('properties.index', [
            'properties' => $properties,
            'filters' => $filters,
            'savedSearches' => $request->user()?->savedSearches()->latest()->get() ?? collect(),
            'recentSearches' => $request->user()?->recentSearches()->latest('last_used_at')->limit(5)->get() ?? collect(),
        ]);
    }

    public function manage(Request $request): View
    {
        $user = $request->user();

        $query = Property::query()->with(['images', 'user', 'company']);

        if ($user->isRole(UserRole::SuperAdmin)) {
            //
        } elseif ($user->isRole(UserRole::Company) && $user->company) {
            $query->where('company_id', $user->company->id);
        } else {
            $query->where('user_id', $user->id);
        }

        return view('properties.manage', [
            'properties' => $query->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('properties.create', [
            'property' => new Property,
        ]);
    }

    public function store(StorePropertyRequest $request, PropertyImageService $propertyImageService): RedirectResponse
    {
        $property = Property::create($this->propertyPayload($request));

        $propertyImageService->sync(
            property: $property,
            uploadedFiles: $request->file('images', []),
            manifest: json_decode((string) $request->input('gallery_manifest'), true, 512, JSON_THROW_ON_ERROR),
            cropPayloads: json_decode((string) $request->input('crop_payloads', '{}'), true, 512, JSON_THROW_ON_ERROR),
            primaryMediaKey: $request->string('primary_media_key')->toString() ?: null,
        );

        return redirect()->route('properties.manage')
            ->with('status', 'Property created and ready for review.');
    }

    public function show(Property $property): View
    {
        $property->load(['images', 'user', 'company']);
        $property->increment('views_count');

        return view('properties.show', [
            'property' => $property,
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorizeManager($property);
        $property->load('images');

        return view('properties.edit', [
            'property' => $property,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property, PropertyImageService $propertyImageService): RedirectResponse
    {
        $this->authorizeManager($property);

        $manifest = json_decode((string) $request->input('gallery_manifest'), true, 512, JSON_THROW_ON_ERROR);
        $totalImages = count($manifest);

        abort_if($totalImages < 5 || $totalImages > 20, 422, 'Each property must have between 5 and 20 images.');

        $property->update($this->propertyPayload($request, $property));

        $propertyImageService->sync(
            property: $property,
            uploadedFiles: $request->file('images', []),
            manifest: $manifest,
            cropPayloads: json_decode((string) $request->input('crop_payloads', '{}'), true, 512, JSON_THROW_ON_ERROR),
            primaryMediaKey: $request->string('primary_media_key')->toString() ?: null,
        );

        return redirect()->route('properties.manage')
            ->with('status', 'Property updated successfully.');
    }

    public function destroy(Property $property, PropertyImageService $propertyImageService): RedirectResponse
    {
        $this->authorizeManager($property);
        $property->load('images');
        $property->images->each(fn ($image) => $propertyImageService->deleteImageRecord($image));
        $property->delete();

        return redirect()->route('properties.manage')
            ->with('status', 'Property deleted.');
    }

    public function report(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        PropertyReport::create([
            'property_id' => $property->id,
            'reported_by_user_id' => $request->user()?->id,
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
        ]);

        return back()->with('status', 'The listing has been reported for review.');
    }

    /**
     * @return array<string, mixed>
     */
    private function propertyPayload(Request $request, ?Property $property = null): array
    {
        $user = $request->user();
        $status = $request->input('status', PropertyStatus::Draft->value);
        $isAdmin = $user->isRole(UserRole::SuperAdmin);

        return [
            'user_id' => $property?->user_id ?? $user->id,
            'company_id' => $user->isRole(UserRole::Company) ? $user->company?->id : $property?->company_id,
            'title' => $request->input('title'),
            'slug' => $property?->slug ?? $this->makeUniqueSlug((string) $request->input('title')),
            'listing_type' => $request->input('listing_type'),
            'listing_source' => $user->isRole(UserRole::Company)
                ? PropertyListingSource::Company->value
                : ($user->isRole(UserRole::Broker) ? PropertyListingSource::Broker->value : PropertyListingSource::Owner->value),
            'status' => $status,
            'moderation_status' => $isAdmin ? PropertyModerationStatus::Approved->value : PropertyModerationStatus::Pending->value,
            'property_type' => $request->input('property_type'),
            'furnishing_status' => $request->input('furnishing_status'),
            'description' => $request->input('description'),
            'bedrooms' => $request->input('bedrooms'),
            'bathrooms' => $request->input('bathrooms'),
            'balconies' => $request->input('balconies'),
            'parking_spaces' => $request->input('parking_spaces'),
            'floors' => $request->input('floors'),
            'year_built' => $request->input('year_built'),
            'area_value' => $request->input('area_value'),
            'area_unit' => $request->input('area_unit', 'sq_ft'),
            'price' => $request->input('price'),
            'security_deposit' => $request->input('security_deposit'),
            'short_term_rate' => $request->input('short_term_rate'),
            'currency' => strtoupper((string) $request->input('currency', 'INR')),
            'available_from' => $request->input('available_from'),
            'has_parking' => $request->boolean('has_parking'),
            'has_pool' => $request->boolean('has_pool'),
            'has_air_conditioning' => $request->boolean('has_air_conditioning'),
            'is_furnished' => $request->boolean('is_furnished'),
            'has_gym' => $request->boolean('has_gym'),
            'has_security' => $request->boolean('has_security'),
            'pets_allowed' => $request->boolean('pets_allowed'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postal_code' => $request->input('postal_code'),
            'country' => $request->input('country', 'India'),
            'locality' => $request->input('locality'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'published_at' => $status === PropertyStatus::Published->value ? now() : $property?->published_at,
            'reviewed_by' => $isAdmin ? $user->id : null,
            'reviewed_at' => $isAdmin ? now() : null,
            'moderation_notes' => $isAdmin ? 'Created or updated directly by admin.' : null,
            'rejection_reason' => null,
        ];
    }

    private function authorizeManager(Property $property): void
    {
        /** @var User $user */
        $user = Auth::user();

        $isAllowed = $user->isRole(UserRole::SuperAdmin)
            || ($user->isRole(UserRole::Company) && $user->company?->id === $property->company_id)
            || $property->user_id === $user->id;

        abort_unless($isAllowed, 403);
    }

    private function makeUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Property::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractFilters(Request $request): array
    {
        return collect([
            'search',
            'property_type',
            'listing_type',
            'status',
            'city',
            'locality',
            'postal_code',
            'min_price',
            'max_price',
            'min_bedrooms',
            'min_bathrooms',
            'min_area',
            'max_area',
            'has_parking',
            'has_pool',
            'has_air_conditioning',
            'is_furnished',
            'has_gym',
            'has_security',
            'pets_allowed',
        ])->mapWithKeys(fn (string $key): array => [$key => $request->input($key)])
            ->filter(fn (mixed $value): bool => $value !== null && $value !== '')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function recordRecentSearch(Request $request, array $filters): void
    {
        if ($request->user() === null || $filters === []) {
            return;
        }

        $request->user()->recentSearches()->create([
            'label' => $filters['search'] ?? ($filters['city'] ?? 'Filtered property search'),
            'filters' => $filters,
            'last_used_at' => now(),
        ]);

        $staleIds = $request->user()->recentSearches()
            ->latest('last_used_at')
            ->skip(8)
            ->pluck('id');

        if ($staleIds->isNotEmpty()) {
            $request->user()->recentSearches()->whereIn('id', $staleIds)->delete();
        }
    }
}
