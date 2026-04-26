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
        Schema::create('page_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(1);
            $table->json('magicbox')->nullable();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug');
            $table->integer('slug_hash');
            $table->text('description')->nullable();
            $table->foreignId('page_category_id')->constrained('page_categories');
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
        Schema::dropIfExists('pages');
        Schema::dropIfExists('page_categories');
    }
};
