<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'Pembaca Pemula', 'icon' => 'book-open', 'min_points' => 0],
            ['name' => 'Kutu Buku', 'icon' => 'library', 'min_points' => 100],
            ['name' => 'Master Baca', 'icon' => 'trophy', 'min_points' => 500],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['name' => $badge['name']], $badge);
        }
    }
}