<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Platform Super Admin',
            'email' => 'admin@realestatemanagement.test',
            'role' => UserRole::SuperAdmin,
        ]);

        $companyUser = User::factory()->create([
            'name' => 'Prime Estates',
            'email' => 'company@realestatemanagement.test',
            'role' => UserRole::Company,
        ]);

        $company = Company::create([
            'user_id' => $companyUser->id,
            'name' => 'Prime Estates',
            'slug' => 'prime-estates',
            'phone' => $companyUser->phone,
            'email' => $companyUser->email,
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
        ]);

        User::factory()->create([
            'name' => 'Owner Demo',
            'email' => 'owner@realestatemanagement.test',
            'role' => UserRole::PropertyOwner,
        ]);

        User::factory()->create([
            'name' => 'Broker Demo',
            'email' => 'broker@realestatemanagement.test',
            'role' => UserRole::Broker,
        ]);

        Property::create([
            'user_id' => $companyUser->id,
            'company_id' => $company->id,
            'title' => 'Sea View Luxury Apartment',
            'slug' => 'sea-view-luxury-apartment',
            'listing_type' => 'sale',
            'listing_source' => 'company',
            'status' => 'published',
            'property_type' => 'Apartment',
            'description' => 'Premium 3 BHK apartment prepared as a seed record for the dashboard.',
            'bedrooms' => 3,
            'bathrooms' => 3,
            'area_value' => 1850,
            'price' => 28500000,
            'address_line_1' => 'Marine Drive',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'country' => 'India',
            'published_at' => now(),
        ]);
    }
}
