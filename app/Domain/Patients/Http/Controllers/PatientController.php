<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PatientController extends Controller
{
    public function index()
    {
        return Patient::with('owner')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id' => 'required|integer',
            'name' => 'required',
            'species' => 'nullable',
            'breed' => 'nullable',
            'age' => 'nullable|integer',
            'sex' => 'nullable'
        ]);
        return Patient::create($data);
    }

    public function show(Patient $patient)
    {
        return $patient->load('owner');
    }

    public function details(Patient $patient)
    {
        return $patient->load([
            'owner',
            'visits.vet',
            'visits.attachments',
            'visits.medications',
            'invoices.lineItems',
            'invoices.payments',
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->update($request->all());
        return $patient;
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->noContent();
    }
}
