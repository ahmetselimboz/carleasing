<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@internal.local');
        $plain = env('SUPER_ADMIN_PASSWORD');

        if ($plain === null || $plain === '') {
            $this->command?->warn('SUPER_ADMIN_PASSWORD .env içinde tanımlı değil; süper admin oluşturulmadı.');

            return;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Süper Admin',
                'password' => Hash::make($plain),
                'role' => 'admin',
                'active' => true,
            ]
        );

        $user->forceFill(['is_super_admin' => true])->save();
    }
}
