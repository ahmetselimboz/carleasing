<?php

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
        Schema::create('car_attribute_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('car_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('car_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->timestamps();
        });
        
        Schema::create('car_attribute_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars');
            $table->foreignId('attribute_id')->constrained('car_attributes');
            $table->foreignId('attribute_category_id')->constrained('car_attribute_categories');
            $table->foreignId('attribute_value_id')->constrained('car_attribute_values');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_attribute_pivots');
        Schema::dropIfExists('car_attribute_values');
        Schema::dropIfExists('car_attribute_categories');
        Schema::dropIfExists('car_attributes');
    }
};
