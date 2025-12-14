<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SpeciesController extends Controller
{
    public function index()
    {
        return Species::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:species,name',
        ]);

        return Species::create($data);
    }

    public function show(Species $species)
    {
        return $species->load('healthPlans');
    }

    public function update(Request $request, Species $species)
    {
        $data = $request->validate([
            'name' => 'required|unique:species,name,' . $species->id,
        ]);

        $species->update($data);

        return $species;
    }

    public function destroy(Species $species)
    {
        $species->delete();

        return response()->noContent();
    }
}
