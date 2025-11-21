<?php

namespace App\Domain\Inventory\Listeners;

use App\Domain\Inventory\Events\EquipmentUsed;
use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Models\StockMovement;

class DeductEquipmentStock
{
    public function handle(EquipmentUsed $event): void
    {
        $payload = $event->payload;
        $itemId = $payload['inventory_item_id'] ?? null;
        $quantity = (int) ($payload['quantity'] ?? 0);

        if ($itemId === null || $quantity <= 0) {
            return;
        }

        $inventoryItem = InventoryItem::with(['batches' => fn($query) => $query
            ->orderBy('expires_at')
            ->orderBy('id')])
            ->find($itemId);

        if ($inventoryItem === null) {
            return;
        }

        $remaining = $quantity;

        foreach ($inventoryItem->batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min($batch->quantity, $remaining);

            if ($deduct <= 0) {
                continue;
            }

            $batch->update(['quantity' => $batch->quantity - $deduct]);
            $remaining -= $deduct;
        }

        $deducted = $quantity - $remaining;

        if ($deducted > 0) {
            StockMovement::create([
                'inventory_item_id' => $inventoryItem->id,
                'change' => -$deducted,
                'reason' => 'equipment_used',
            ]);
        }
    }
}
