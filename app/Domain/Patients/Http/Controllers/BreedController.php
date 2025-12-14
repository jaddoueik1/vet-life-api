<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\Breed;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BreedController extends Controller
{
    public function index()
    {
        return Breed::with(['species', 'healthPlan'])->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'species_id' => 'required|exists:species,id',
            'health_plan_id' => 'nullable|exists:health_plans,id',
            'name' => 'required|string',
        ]);

        return Breed::create($data);
    }

    public function show(Breed $breed)
    {
        return $breed->load(['species', 'healthPlan']);
    }

    public function update(Request $request, Breed $breed)
    {
        $data = $request->validate([
            'species_id' => 'exists:species,id',
            'health_plan_id' => 'nullable|exists:health_plans,id',
            'name' => 'string',
        ]);

        $breed->update($data);

        return $breed;
    }

    public function destroy(Breed $breed)
    {
        $breed->delete();

        return response()->noContent();
    }
}
