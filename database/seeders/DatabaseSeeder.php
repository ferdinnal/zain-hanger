<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Buat superadmin default jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@zainhanger.com'],
            [
                'name'              => 'Admin Zain',
                'password'          => Hash::make('admin123'),
                'role'              => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ DatabaseSeeder selesai!');
        $this->command->info('   Admin login: admin@zainhanger.com / admin123');
        $this->command->info('   URL Admin:   http://localhost:8000/admin');
    }
}
