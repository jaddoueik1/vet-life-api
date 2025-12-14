<?php

namespace Database\Seeders;

use App\Domain\Patients\Models\HealthPlan;
use App\Domain\Patients\Models\Species;
use App\Domain\Patients\Models\Vaccination;
use Illuminate\Database\Seeder;

class HealthPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Dog Plan
        $dogSpecies = Species::where('name', 'Dog')->first();
        if ($dogSpecies) {
            $plan = HealthPlan::firstOrCreate([
                'species_id' => $dogSpecies->id,
                'name' => 'Standard Dog Plan',
                'description' => 'Core vaccinations for dogs.',
            ]);

            $rabies = Vaccination::where('name', 'Rabies')->first();
            $dhpp = Vaccination::where('name', 'DHPP')->first();

            // Attach with schedule
            if ($rabies) $plan->vaccinations()->syncWithoutDetaching([$rabies->id => ['frequency_days' => 365, 'start_age_weeks' => 12]]);
            if ($dhpp) $plan->vaccinations()->syncWithoutDetaching([$dhpp->id => ['frequency_days' => 365, 'start_age_weeks' => 8]]);
        }

        // Cat Plan
        $catSpecies = Species::where('name', 'Cat')->first();
        if ($catSpecies) {
            $plan = HealthPlan::firstOrCreate([
                'species_id' => $catSpecies->id,
                'name' => 'Standard Cat Plan',
                'description' => 'Core vaccinations for cats.',
            ]);

            $rabies = Vaccination::where('name', 'Rabies')->first();
            $fvrcp = Vaccination::where('name', 'FVRCP')->first();

            // Attach with schedule
            if ($rabies) $plan->vaccinations()->syncWithoutDetaching([$rabies->id => ['frequency_days' => 365, 'start_age_weeks' => 12]]);
            if ($fvrcp) $plan->vaccinations()->syncWithoutDetaching([$fvrcp->id => ['frequency_days' => 365, 'start_age_weeks' => 8]]);
        }
    }
}
