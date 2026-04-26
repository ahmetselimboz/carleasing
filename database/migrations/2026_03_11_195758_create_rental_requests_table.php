<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->integer('requested_car_count')->nullable();
            $table->integer('company_total_car_count')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number_or_tckn')->nullable();
            $table->json('cars')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamp('read_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_requests');
    }
};
