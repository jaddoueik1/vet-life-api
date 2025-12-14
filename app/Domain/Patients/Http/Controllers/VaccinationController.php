<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\Vaccination;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VaccinationController extends Controller
{
    public function index()
    {
        return Vaccination::paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:vaccinations,name',
            'description' => 'nullable|string',
        ]);

        return Vaccination::create($data);
    }

    public function show(Vaccination $vaccination)
    {
        return $vaccination;
    }

    public function update(Request $request, Vaccination $vaccination)
    {
        $data = $request->validate([
            'name' => 'required|unique:vaccinations,name,' . $vaccination->id,
            'description' => 'nullable|string',
        ]);

        $vaccination->update($data);

        return $vaccination;
    }

    public function destroy(Vaccination $vaccination)
    {
        $vaccination->delete();

        return response()->noContent();
    }
}
