<?php

namespace Database\Seeders;

use App\Domain\Patients\Models\Breed;
use App\Domain\Patients\Models\HealthPlan;
use App\Domain\Patients\Models\Species;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    public function run(): void
    {
        $breeds = [
            'Dog' => ['Golden Retriever', 'Labrador Retriever', 'German Shepherd', 'Bulldog', 'Poodle', 'Beagle', 'Border Collie'],
            'Cat' => ['Persian', 'Siamese', 'Maine Coon', 'Bengal', 'Sphynx'],
            'Bird' => ['Parrot', 'Canary', 'Finch', 'Cockatiel'],
            'Rabbit' => ['Holland Lop', 'Netherland Dwarf'],
            'Hamster' => ['Syrian', 'Dwarf'],
        ];

        foreach ($breeds as $speciesName => $breedNames) {
            $species = Species::where('name', $speciesName)->first();
            $plan = HealthPlan::where('species_id', $species?->id)->first();

            if ($species) {
                foreach ($breedNames as $breedName) {
                    Breed::firstOrCreate([
                        'species_id' => $species->id,
                        'name' => $breedName,
                    ], [
                        'health_plan_id' => $plan?->id,
                    ]);
                }
            }
        }
    }
}
