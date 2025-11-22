<?php

namespace App\Domain\Medications\Http\Controllers;

use App\Domain\Medications\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class MedicationController extends Controller
{
    public function index()
    {
        return Medication::with('vendors')->paginate();
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $vendorData = $data['vendors'] ?? [];

        $medication = Medication::create(Arr::except($data, ['vendors']));
        $this->syncVendors($medication, $vendorData);

        return $medication->load('vendors');
    }

    public function show(Medication $medication)
    {
        return $medication->load('vendors');
    }

    public function update(Request $request, Medication $medication)
    {
        $data = $this->validatePayload($request, true, $medication->id);
        $vendorData = $data['vendors'] ?? null;

        $medication->update(Arr::except($data, ['vendors']));

        if ($vendorData !== null) {
            $this->syncVendors($medication, $vendorData);
        }

        return $medication->load('vendors');
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();

        return response()->noContent();
    }

    private function validatePayload(Request $request, bool $isUpdate = false, ?int $medicationId = null): array
    {
        return $request->validate([
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'sku' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::unique('medications', 'sku')->ignore($medicationId),
            ],
            'description' => $isUpdate ? 'sometimes|nullable|string' : 'nullable|string',
            'price' => $isUpdate ? 'sometimes|numeric|min:0' : 'nullable|numeric|min:0',
            'current_stock' => $isUpdate ? 'sometimes|integer|min:0' : 'nullable|integer|min:0',
            'reorder_level' => $isUpdate ? 'sometimes|integer|min:0' : 'nullable|integer|min:0',
            'vendors' => 'array',
            'vendors.*.vendor_id' => 'required|exists:vendors,id',
            'vendors.*.is_primary' => 'boolean',
        ]);
    }

    private function syncVendors(Medication $medication, array $vendors): void
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

        $medication->vendors()->sync($pivotData);
    }
}
