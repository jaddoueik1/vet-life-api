<?php

namespace App\Domain\Inventory\Http\Controllers;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $service)
    {
    }

    public function index()
    {
        return InventoryItem::with('vendors')->paginate();
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $vendorData = $data['vendors'] ?? [];

        $item = $this->service->createItem(Arr::except($data, ['vendors']));
        $this->syncVendors($item, $vendorData);

        return $item->load('vendors');
    }

    public function show(InventoryItem $inventoryItem)
    {
        return $inventoryItem->load('vendors');
    }

    public function lowStock()
    {
        return $this->service->lowStockItems();
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $data = $this->validatePayload($request, true);
        $vendorData = $data['vendors'] ?? null;

        $inventoryItem->update(Arr::except($data, ['vendors']));

        if ($vendorData !== null) {
            $this->syncVendors($inventoryItem, $vendorData);
        }

        return $inventoryItem->load('vendors');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return response()->noContent();
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'sku' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'reorder_level' => 'nullable|integer',
            'vendors' => 'array',
            'vendors.*.vendor_id' => 'required|exists:vendors,id',
            'vendors.*.is_primary' => 'boolean',
        ]);
    }

    private function syncVendors(InventoryItem $inventoryItem, array $vendors): void
    {
        $primaryCount = collect($vendors)->where('is_primary', true)->count();

        if ($primaryCount > 1) {
            throw ValidationException::withMessages([
                'vendors' => 'Only one primary vendor may be assigned.',
            ]);
        }

        $pivotData = collect($vendors)->mapWithKeys(function (array $vendor) {
            return [
                $vendor['vendor_id'] => ['is_primary' => $vendor['is_primary'] ?? false],
            ];
        });

        $inventoryItem->vendors()->sync($pivotData);
    }
}
