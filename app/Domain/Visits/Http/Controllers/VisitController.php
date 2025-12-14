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
        return Visit::with(['patient', 'vet', 'services', 'medications', 'equipmentUsed'])->paginate();
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
            'equipment_used_ids' => 'nullable|array',
        ]);

        return $this->service->create($data);
    }

    public function show(Visit $visit)
    {
        return $visit->load(['patient', 'vet', 'services', 'medications', 'equipmentUsed']);
    }

    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'summary' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'visit_date' => 'sometimes|date',
            'status' => 'sometimes|string|in:DRAFT,COMPLETE',
            'visit_reason' => 'nullable|string',
            'exam_findings' => 'nullable|string',
            'care_plan' => 'nullable|string',
        ]);

        $originalStatus = $visit->status;
        $visit->update($validated);

        if ($originalStatus !== Visit::STATUS_COMPLETE && $visit->status === Visit::STATUS_COMPLETE) {
            \App\Domain\Visits\Events\VisitCompleted::dispatch($visit);
        }

        return $visit;
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();
        return response()->noContent();
    }
}
