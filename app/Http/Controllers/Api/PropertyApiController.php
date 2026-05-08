<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PropertyController;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\PropertyImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = collect($request->all())
            ->only([
                'search', 'property_type', 'listing_type', 'status', 'city', 'locality', 'postal_code',
                'min_price', 'max_price', 'min_bedrooms', 'min_bathrooms', 'min_area', 'max_area',
                'has_parking', 'has_pool', 'has_air_conditioning', 'is_furnished', 'has_gym', 'has_security', 'pets_allowed',
            ])->filter(fn ($value) => $value !== null && $value !== '')
            ->all();

        $properties = Property::query()
            ->with(['images', 'user', 'company'])
            ->publiclyVisible()
            ->applyFilters($filters)
            ->applySort($request->string('sort')->toString())
            ->paginate(12)
            ->withQueryString();

        return response()->json($properties);
    }

    public function store(StorePropertyRequest $request, PropertyImageService $propertyImageService): JsonResponse
    {
        $controller = app(PropertyController::class);
        $response = $controller->store($request, $propertyImageService);

        return response()->json(['message' => 'Property created successfully.'], 201);
    }

    public function show(Property $property): JsonResponse
    {
        return response()->json($property->load(['images', 'user', 'company', 'inquiries', 'leads']));
    }

    public function update(UpdatePropertyRequest $request, Property $property, PropertyImageService $propertyImageService): JsonResponse
    {
        $controller = app(PropertyController::class);
        $controller->update($request, $property, $propertyImageService);

        return response()->json(['message' => 'Property updated successfully.']);
    }

    public function destroy(Property $property, PropertyImageService $propertyImageService): JsonResponse
    {
        $controller = app(PropertyController::class);
        $controller->destroy($property, $propertyImageService);

        return response()->json(['message' => 'Property deleted successfully.']);
    }
}
