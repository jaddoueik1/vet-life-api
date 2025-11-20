<?php

namespace App\Domain\Inventory\Services;

use App\Domain\Inventory\Events\StockLow;
use App\Domain\Inventory\Models\InventoryItem;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

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

    public function lowStockItems()
    {
        return InventoryItem::query()
            ->select('inventory_items.*')
            ->withSum('batches as stock_on_hand', 'quantity')
            ->whereNotNull('reorder_level')
            ->havingRaw('COALESCE(stock_on_hand, 0) <= reorder_level')
            ->get();
    }
}
