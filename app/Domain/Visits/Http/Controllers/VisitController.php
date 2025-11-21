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
        return Visit::with(['patient', 'vet', 'services', 'medications'])->paginate();
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
            'medications' => 'nullable|array',
            'medications.*.medication_id' => 'required_with:medications|integer|exists:medications,id',
            'medications.*.quantity' => 'nullable|integer|min:1',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required_with:services|integer|exists:services,id',
            'services.*.quantity' => 'nullable|integer|min:1',
        ]);

        return $this->service->create($data);
    }

    public function show(Visit $visit)
    {
        return $visit->load(['patient', 'vet', 'services', 'medications']);
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
