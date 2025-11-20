<?php

namespace App\Domain\Inventory\Services;

use App\Domain\Inventory\Events\StockLow;
use App\Domain\Inventory\Models\InventoryItem;
use Illuminate\Support\Facades\Event;

class InventoryService
{
    public function createItem(array $data): InventoryItem
    {
        $item = InventoryItem::create($data);
        return $item;
    }

    public function adjustStock(InventoryItem $item, int $change): void
    {
        $item->update(['reorder_level' => $item->reorder_level]);
        if ($item->reorder_level && $change < 0 && $item->reorder_level > 0) {
            Event::dispatch(new StockLow($item->toArray()));
        }
    }
}
