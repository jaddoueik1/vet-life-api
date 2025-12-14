<?php

namespace Database\Seeders;

use App\Domain\Patients\Models\Vaccination;
use Illuminate\Database\Seeder;

class VaccinationSeeder extends Seeder
{
    public function run(): void
    {
        $vaccinations = [
            ['name' => 'Rabies', 'description' => 'Core vaccine against Rabies virus.'],
            ['name' => 'DHPP', 'description' => 'Distemper, Hepatitis, Parvovirus, Parainfluenza.'],
            ['name' => 'Bordetella', 'description' => 'Kennel cough vaccine.'],
            ['name' => 'Leptospirosis', 'description' => 'Bacterial infection vaccine.'],
            ['name' => 'FVRCP', 'description' => 'Feline Viral Rhinotracheitis, Calicivirus, Panleukopenia.'],
            ['name' => 'FeLV', 'description' => 'Feline Leukemia Virus.'],
        ];

        foreach ($vaccinations as $vaccine) {
            Vaccination::firstOrCreate(['name' => $vaccine['name']], $vaccine);
        }
    }
}
