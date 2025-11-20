<?php

namespace App\Domain\Inventory\Http\Controllers;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $service)
    {
    }

    public function index()
    {
        return InventoryItem::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'sku' => 'required',
            'reorder_level' => 'nullable|integer'
        ]);
        return $this->service->createItem($data);
    }

    public function show(InventoryItem $inventoryItem)
    {
        return $inventoryItem;
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $inventoryItem->update($request->all());
        return $inventoryItem;
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return response()->noContent();
    }
}
