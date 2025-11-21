<?php

namespace Database\Seeders;

use App\Domain\Medications\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            ['name' => 'Amoxicillin', 'description' => 'Broad-spectrum antibiotic', 'unit_price' => 15.50],
            ['name' => 'Carprofen', 'description' => 'Anti-inflammatory for pain relief', 'unit_price' => 22.75],
            ['name' => 'Dewormer', 'description' => 'Intestinal parasite treatment', 'unit_price' => 12.00],
        ];

        foreach ($medications as $medication) {
            Medication::updateOrCreate(['name' => $medication['name']], $medication);
        }
    }
}
