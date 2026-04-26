<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Eski varsayılan 'user' rolünü panel personeli için Müşteri Hizmetleri ile hizala.
     */
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'user')
            ->where('is_super_admin', false)
            ->update(['role' => 'customer_service']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'customer_service')
            ->where('is_super_admin', false)
            ->update(['role' => 'user']);
    }
};
