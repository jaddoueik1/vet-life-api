<?php

namespace App\Domain\Visits\Http\Controllers;

use App\Domain\Visits\Models\Visit;
use App\Domain\Visits\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VisitController extends Controller
{
    public function __construct(private VisitService $service)
    {
    }

    public function index()
    {
        return Visit::with(['patient', 'vet', 'services', 'medications', 'equipment'])->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'vet_id' => 'required|integer',
            'summary' => 'nullable',
            'diagnosis' => 'nullable',
            'treatment' => 'nullable',
            'visit_date' => 'required|date',
            'medication_ids' => 'nullable|array',
            'service_ids' => 'nullable|array',
            'equipment_ids' => 'nullable|array',
        ]);

        return $this->service->create($data);
    }

    public function show(Visit $visit)
    {
        return $visit->load(['patient', 'vet', 'services', 'medications', 'equipment']);
    }

    public function update(Request $request, Visit $visit)
    {
        $visit->update($request->all());
        return $visit;
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();
        return response()->noContent();
    }
}
