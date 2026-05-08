<?php

namespace App\Models;

use App\Enums\PropertyListingSource;
use App\Enums\PropertyListingType;
use App\Enums\PropertyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'property_type',
        'furnishing_status',
        'description',
        'bedrooms',
        'bathrooms',
        'balconies',
        'parking_spaces',
        'area_value',
        'area_unit',
        'price',
        'security_deposit',
        'short_term_rate',
        'currency',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'locality',
        'latitude',
        'longitude',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'listing_type' => PropertyListingType::class,
            'listing_source' => PropertyListingSource::class,
            'status' => PropertyStatus::class,
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'security_deposit' => 'decimal:2',
            'short_term_rate' => 'decimal:2',
            'area_value' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
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
}
