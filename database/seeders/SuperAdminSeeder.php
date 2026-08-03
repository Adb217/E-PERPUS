<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::firstOrCreate(
            ['email' => 'guru@smkkosgorobogor.sch.id'],
            [
                'name' => 'Admin Perpustakaan',
                'password' => Hash::make('password123'), // nanti ganti pas production
                'email_verified_at' => now(),
            ]
        );

        $superadmin->assignRole('superadmin');
    }
}