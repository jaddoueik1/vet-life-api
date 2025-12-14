<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndUsersSeeder::class,
            SpeciesSeeder::class,
            VaccinationSeeder::class,
            HealthPlanSeeder::class,
            BreedSeeder::class,
            ClinicDataSeeder::class,
            InventorySeeder::class,
            MedicationSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
