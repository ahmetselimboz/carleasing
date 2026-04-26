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
        Schema::create('car_price_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars');
            $table->foreignId('car_package_id')->constrained('car_packages');
            $table->foreignId('car_duration_id')->constrained('car_durations');
            $table->foreignId('car_kilometer_option_id')->constrained('car_kilometer_options');
            $table->foreignId('car_down_payment_id')->constrained('car_down_payments');
            $table->string('monthly_price');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_price_matrix');
    }
};
