<?php

use App\Enums\PropertyListingSource;
use App\Enums\PropertyListingType;
use App\Enums\PropertyStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('listing_type')->default(PropertyListingType::Sale->value);
            $table->string('listing_source')->default(PropertyListingSource::Owner->value);
            $table->string('status')->default(PropertyStatus::Draft->value);
            $table->string('property_type');
            $table->string('furnishing_status')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('bedrooms')->nullable();
            $table->unsignedSmallInteger('bathrooms')->nullable();
            $table->unsignedSmallInteger('balconies')->nullable();
            $table->unsignedSmallInteger('parking_spaces')->nullable();
            $table->decimal('area_value', 10, 2)->nullable();
            $table->string('area_unit', 20)->default('sq_ft');
            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('security_deposit', 14, 2)->nullable();
            $table->decimal('short_term_rate', 14, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code', 20)->nullable();
            $table->string('country')->default('India');
            $table->string('locality')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
