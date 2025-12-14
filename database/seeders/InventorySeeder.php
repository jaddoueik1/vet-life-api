<?php

namespace Database\Seeders;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Models\PurchaseOrder;
use App\Domain\Inventory\Models\StockBatch;
use App\Domain\Inventory\Models\StockMovement;
use App\Domain\Inventory\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Healthy Pet Supplies', 'email' => 'orders@healthypets.com', 'phone' => '555-4001'],
            ['name' => 'Vet Pharma', 'email' => 'sales@vetpharma.com', 'phone' => '555-4002'],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::updateOrCreate(
                ['name' => $supplierData['name']],
                $supplierData
            );
        }

        $items = [
            ['name' => 'Flea & Tick Prevention', 'sku' => 'FT-100', 'reorder_level' => 10, 'quantity' => 25, 'price' => 15.00],
            ['name' => 'Heartworm Tablets', 'sku' => 'HW-200', 'reorder_level' => 15, 'quantity' => 40, 'price' => 20.00],
            ['name' => 'Gauze Pads', 'sku' => 'GA-300', 'reorder_level' => 30, 'quantity' => 100, 'price' => 5.00],
        ];

        foreach ($items as $itemData) {
            InventoryItem::updateOrCreate(
                ['sku' => $itemData['sku']],
                $itemData
            );
        }

        $itemMap = InventoryItem::whereIn('sku', array_column($items, 'sku'))
            ->get()
            ->keyBy('sku');

        $batchData = [
            ['inventory_item_id' => $itemMap['FT-100']->id, 'quantity' => 25, 'expires_at' => Carbon::now()->addMonths(8)],
            ['inventory_item_id' => $itemMap['HW-200']->id, 'quantity' => 40, 'expires_at' => Carbon::now()->addMonths(12)],
            ['inventory_item_id' => $itemMap['GA-300']->id, 'quantity' => 100, 'expires_at' => null],
        ];

        foreach ($batchData as $batch) {
            StockBatch::updateOrCreate(
                [
                    'inventory_item_id' => $batch['inventory_item_id'],
                    'expires_at' => $batch['expires_at'],
                ],
                ['quantity' => $batch['quantity']]
            );
        }

        $movements = [
            ['inventory_item_id' => $itemMap['FT-100']->id, 'change' => -5, 'reason' => 'Dispensed during consultations'],
            ['inventory_item_id' => $itemMap['HW-200']->id, 'change' => -10, 'reason' => 'Sold with prescription'],
            ['inventory_item_id' => $itemMap['GA-300']->id, 'change' => 20, 'reason' => 'Restocked from supplier'],
        ];

        foreach ($movements as $movement) {
            StockMovement::create($movement);
        }

        $supplierId = Supplier::where('name', 'Vet Pharma')->value('id');
        if ($supplierId) {
            PurchaseOrder::updateOrCreate(
                ['supplier_id' => $supplierId, 'status' => 'pending'],
                ['total' => 320.00]
            );
        }
    }
}
