<?php

namespace Database\Seeders;

use App\Domain\Medications\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            ['name' => 'Amoxicillin', 'category' => 'Broad-spectrum antibiotic', 'price' => 15.50, 'current_stock' => 100, 'reorder_level' => 20, 'sku' => 'AMOX-1001'],
            ['name' => 'Prednisone', 'category' => 'Corticosteroid for inflammation', 'price' => 18.00, 'current_stock' => 80, 'reorder_level' => 15, 'sku' => 'PRED-2002'],
            ['name' => 'Furosemide', 'category' => 'Diuretic for heart conditions', 'price' => 10.25, 'current_stock' => 60, 'reorder_level' => 10, 'sku' => 'FURO-3003'],
            ['name' => 'Metronidazole', 'category' => 'Antibiotic for gastrointestinal infections', 'price' => 12.75, 'current_stock' => 90, 'reorder_level' => 18, 'sku' => 'METRO-4004'],
            ['name' => 'Carprofen', 'category' => 'NSAID for pain relief', 'price' => 20.00, 'current_stock' => 70, 'reorder_level' => 12, 'sku' => 'CARP-5005'],
        ];

        foreach ($medications as $medication) {
            Medication::updateOrCreate(['name' => $medication['name']], $medication);
        }
    }
}
