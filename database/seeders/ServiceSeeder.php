<?php

namespace Database\Seeders;

use App\Domain\Services\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Wellness Exam', 'description' => 'Comprehensive physical examination', 'price' => 45.00],
            ['name' => 'Vaccination', 'description' => 'Core vaccine administration', 'price' => 35.00],
            ['name' => 'Dental Cleaning', 'description' => 'Routine dental scaling and polishing', 'price' => 120.00],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
