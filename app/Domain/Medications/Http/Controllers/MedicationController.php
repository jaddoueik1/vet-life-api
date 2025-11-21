<?php

namespace App\Domain\Medications\Http\Controllers;

use App\Domain\Medications\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MedicationController extends Controller
{
    public function index()
    {
        return Medication::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:medications,sku',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
        ]);

        return Medication::create($data);
    }

    public function show(Medication $medication)
    {
        return $medication;
    }

    public function update(Request $request, Medication $medication)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string',
            'sku' => 'sometimes|required|string|unique:medications,sku,' . $medication->id,
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'current_stock' => 'sometimes|integer|min:0',
            'reorder_level' => 'sometimes|integer|min:0',
        ]);

        $medication->update($data);

        return $medication;
    }

    public function destroy(Medication $medication)
    {
        $medication->delete();

        return response()->noContent();
    }
}
