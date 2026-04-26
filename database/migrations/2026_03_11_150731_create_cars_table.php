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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug');
            $table->integer('slug_hash');
            $table->text('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission_type')->nullable();
            $table->string('body_type')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(0);
            $table->json( 'magicbox')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
