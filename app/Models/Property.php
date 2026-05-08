<?php

namespace App\Models;

use App\Enums\PropertyListingSource;
use App\Enums\PropertyListingType;
use App\Enums\PropertyModerationStatus;
use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'slug',
        'listing_type',
        'listing_source',
        'status',
        'moderation_status',
        'property_type',
        'furnishing_status',
        'description',
        'bedrooms',
        'bathrooms',
        'balconies',
        'parking_spaces',
        'floors',
        'year_built',
        'area_value',
        'area_unit',
        'price',
        'security_deposit',
        'short_term_rate',
        'currency',
        'available_from',
        'has_parking',
        'has_pool',
        'has_air_conditioning',
        'is_furnished',
        'has_gym',
        'has_security',
        'pets_allowed',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'locality',
        'latitude',
        'longitude',
        'views_count',
        'moderation_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'listing_type' => PropertyListingType::class,
            'listing_source' => PropertyListingSource::class,
            'status' => PropertyStatus::class,
            'moderation_status' => PropertyModerationStatus::class,
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'available_from' => 'date',
            'price' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'short_term_rate' => 'decimal:2',
            'area_value' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'has_parking' => 'boolean',
            'has_pool' => 'boolean',
            'has_air_conditioning' => 'boolean',
            'is_furnished' => 'boolean',
            'has_gym' => 'boolean',
            'has_security' => 'boolean',
            'pets_allowed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PropertyReport::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('moderation_status', PropertyModerationStatus::Approved->value)
            ->whereIn('status', [
                PropertyStatus::Published->value,
                PropertyStatus::Sold->value,
                PropertyStatus::Rented->value,
            ]);
    }

    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $like = "%{$search}%";

                    $query->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('address_line_1', 'like', $like)
                        ->orWhere('address_line_2', 'like', $like)
                        ->orWhere('city', 'like', $like)
                        ->orWhere('state', 'like', $like)
                        ->orWhere('postal_code', 'like', $like)
                        ->orWhere('locality', 'like', $like);
                });
            })
            ->when($filters['property_type'] ?? null, fn (Builder $query, string $value) => $query->where('property_type', $value))
            ->when($filters['listing_type'] ?? null, fn (Builder $query, string $value) => $query->where('listing_type', $value))
            ->when($filters['status'] ?? null, fn (Builder $query, string $value) => $query->where('status', $value))
            ->when($filters['city'] ?? null, fn (Builder $query, string $value) => $query->where('city', 'like', "%{$value}%"))
            ->when($filters['locality'] ?? null, fn (Builder $query, string $value) => $query->where('locality', 'like', "%{$value}%"))
            ->when($filters['postal_code'] ?? null, fn (Builder $query, string $value) => $query->where('postal_code', 'like', "%{$value}%"))
            ->when($filters['min_price'] ?? null, fn (Builder $query, mixed $value) => $query->where('price', '>=', (float) $value))
            ->when($filters['max_price'] ?? null, fn (Builder $query, mixed $value) => $query->where('price', '<=', (float) $value))
            ->when($filters['min_bedrooms'] ?? null, fn (Builder $query, mixed $value) => $query->where('bedrooms', '>=', (int) $value))
            ->when($filters['min_bathrooms'] ?? null, fn (Builder $query, mixed $value) => $query->where('bathrooms', '>=', (int) $value))
            ->when($filters['min_area'] ?? null, fn (Builder $query, mixed $value) => $query->where('area_value', '>=', (float) $value))
            ->when($filters['max_area'] ?? null, fn (Builder $query, mixed $value) => $query->where('area_value', '<=', (float) $value))
            ->when(filter_var($filters['has_parking'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('has_parking', filter_var($filters['has_parking'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['has_pool'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('has_pool', filter_var($filters['has_pool'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['has_air_conditioning'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('has_air_conditioning', filter_var($filters['has_air_conditioning'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['is_furnished'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('is_furnished', filter_var($filters['is_furnished'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['has_gym'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('has_gym', filter_var($filters['has_gym'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['has_security'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('has_security', filter_var($filters['has_security'], FILTER_VALIDATE_BOOLEAN)))
            ->when(filter_var($filters['pets_allowed'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null, fn (Builder $query) => $query->where('pets_allowed', filter_var($filters['pets_allowed'], FILTER_VALIDATE_BOOLEAN)));
    }

    public function scopeApplySort(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'oldest' => $query->oldest(),
            'popularity' => $query->orderByDesc('views_count')->orderByDesc('published_at'),
            default => $query->latest('published_at')->latest(),
        };
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line_1,
            $this->address_line_2,
            $this->locality,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ])->filter()->implode(', ');
    }
}
