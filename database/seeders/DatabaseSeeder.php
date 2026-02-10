<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat User Admin Default
        User::updateOrCreate(
            ['email' => 'adminkaigo@gmail.com'],
            [
                'name' => 'Admin Kaigo',
                'password' => Hash::make('admin123'),
            ]
        );

        // Isi Settings Awal agar Logic ShikenController tidak patah
        Setting::updateOrCreate(
            ['key' => 'cbt_access_key'],
            ['label' => 'Kunci Akses Ujian', 'value' => 'KAIGO2026']
        );

        Setting::updateOrCreate(
            ['key' => 'default_timer'],
            ['label' => 'Timer Default (Detik)', 'value' => '60']
        );
    }
}