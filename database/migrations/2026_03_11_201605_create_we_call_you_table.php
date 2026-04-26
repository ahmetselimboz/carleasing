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
        Schema::create('we_call_you', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('city')->nullable();
            $table->integer('requested_car_count')->nullable();
            $table->integer('car_park_count')->nullable();
            $table->boolean('is_active')->default(1);
            $table->json('magicbox')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('we_call_you');
    }
};
