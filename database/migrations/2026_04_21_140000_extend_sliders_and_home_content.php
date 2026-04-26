<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (! Schema::hasColumn('sliders', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('id');
            }
            if (! Schema::hasColumn('sliders', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('sort_order');
            }
            if (! Schema::hasColumn('sliders', 'badge')) {
                $table->string('badge')->nullable()->after('title');
            }
            if (! Schema::hasColumn('sliders', 'title_highlight')) {
                $table->string('title_highlight')->nullable()->after('badge');
            }
            if (! Schema::hasColumn('sliders', 'subtitle')) {
                $table->text('subtitle')->nullable()->after('description');
            }
        });

        Schema::table('cars', function (Blueprint $table) {
            if (! Schema::hasColumn('cars', 'home_featured')) {
                $table->boolean('home_featured')->default(false)->after('status');
            }
            if (! Schema::hasColumn('cars', 'home_sort_order')) {
                $table->unsignedSmallInteger('home_sort_order')->nullable()->after('home_featured');
            }
        });

        if (! Schema::hasTable('home_service_tiles')) {
            Schema::create('home_service_tiles', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('image')->nullable();
                $table->string('icon')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('link_url')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('home_partners')) {
            Schema::create('home_partners', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('home_testimonials')) {
            Schema::create('home_testimonials', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->string('name');
                $table->string('role')->nullable();
                $table->string('avatar')->nullable();
                $table->text('quote');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->timestamps();
            });
        }

        Schema::table('faqs', function (Blueprint $table) {
            if (! Schema::hasColumn('faqs', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('id');
            }
            if (! Schema::hasColumn('faqs', 'answer_body')) {
                $table->text('answer_body')->nullable()->after('answer');
            }
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            if (Schema::hasColumn('faqs', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('faqs', 'answer_body')) {
                $table->dropColumn('answer_body');
            }
        });

        Schema::dropIfExists('home_testimonials');
        Schema::dropIfExists('home_partners');
        Schema::dropIfExists('home_service_tiles');

        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'home_featured')) {
                $table->dropColumn('home_featured');
            }
            if (Schema::hasColumn('cars', 'home_sort_order')) {
                $table->dropColumn('home_sort_order');
            }
        });

        Schema::table('sliders', function (Blueprint $table) {
            foreach (['subtitle', 'title_highlight', 'badge', 'is_active', 'sort_order'] as $col) {
                if (Schema::hasColumn('sliders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
