<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('we_call_you', function (Blueprint $table) {
            $table->foreignId('car_id')->nullable()->after('phone_number')
                ->constrained('cars')->nullOnDelete();
            $table->string('preferred_time', 64)->nullable()->after('city');
            $table->text('note')->nullable()->after('preferred_time');
            $table->timestamp('read_at')->nullable()->after('is_active');
            $table->ipAddress('ip_address')->nullable()->after('read_at');
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('we_call_you', function (Blueprint $table) {
            $table->dropConstrainedForeignId('car_id');
            $table->dropColumn(['preferred_time', 'note', 'read_at', 'ip_address', 'user_agent']);
        });
    }
};
