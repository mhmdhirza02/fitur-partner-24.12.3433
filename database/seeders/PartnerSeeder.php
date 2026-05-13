<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Partner::create([
                'name' => fake()->company(),
                'logo_url' => 'https://placehold.co/200x200',
            ]);
        }
    }
}