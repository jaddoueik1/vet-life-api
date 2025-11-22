<?php

namespace Database\Seeders;

use App\Domain\Medications\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            [
                'name' => 'Amoxicillin',
                'sku' => 'AMX-001',
                'strength' => '250mg',
                'category' => 'Antibiotic',
                'dosage' => 'Twice daily',
                'price' => 15.50,
                'current_stock' => 150,
                'reorder_level' => 30,
            ],
            [
                'name' => 'Carprofen',
                'sku' => 'CRP-001',
                'strength' => '25mg',
                'category' => 'Pain Relief',
                'dosage' => 'Once daily',
                'price' => 22.75,
                'current_stock' => 80,
                'reorder_level' => 20,
            ],
            [
                'name' => 'Dewormer',
                'sku' => 'DWM-001',
                'strength' => '50mg',
                'category' => 'Antiparasitic',
                'dosage' => 'Single dose',
                'price' => 12.00,
                'current_stock' => 200,
                'reorder_level' => 40,
            ],
        ];

        foreach ($medications as $medication) {
            Medication::updateOrCreate(['name' => $medication['name']], $medication);
        }
    }
}
