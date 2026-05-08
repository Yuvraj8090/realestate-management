<?php

namespace App\Http\Controllers\Api;

use App\Enums\PropertyModerationStatus;
use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PropertyController;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PropertyApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
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

        return PropertyResource::collection($properties);
    }

    public function store(StorePropertyRequest $request, PropertyImageService $propertyImageService): JsonResponse
    {
        $controller = app(PropertyController::class);
        $controller->store($request, $propertyImageService);

        return response()->json(['message' => 'Property created successfully.'], 201);
    }

    public function show(Request $request, Property $property): PropertyResource
    {
        $user = $request->user();
        $isVisible = $property->moderation_status === PropertyModerationStatus::Approved
            && in_array($property->status, [PropertyStatus::Published, PropertyStatus::Sold, PropertyStatus::Rented], true);

        $canManage = $user !== null && (
            $user->isRole(UserRole::SuperAdmin)
            || ($user->isRole(UserRole::Company) && $user->company?->id === $property->company_id)
            || $property->user_id === $user->id
        );

        abort_unless($isVisible || $canManage, 404);

        $relations = ['images', 'user.company', 'company', 'reviewer'];

        if ($canManage) {
            $relations[] = 'inquiries';
            $relations[] = 'leads';
        }

        return new PropertyResource($property->load($relations));
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
