<?php

namespace Database\Seeders;

use App\Domain\Patients\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $species = ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster'];

        foreach ($species as $name) {
            Species::firstOrCreate(['name' => $name]);
        }
    }
}
