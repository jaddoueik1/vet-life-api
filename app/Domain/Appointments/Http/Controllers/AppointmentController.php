<?php

namespace App\Domain\Appointments\Http\Controllers;

use App\Domain\Appointments\Models\Appointment;
use App\Domain\Appointments\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $service)
    {
    }

    public function index()
    {
        return Appointment::with(['patient', 'vet'])->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer',
            'scheduled_at' => 'required|date',
            'status' => 'required',
            'notes' => 'nullable',
            'assigned_vet_id' => 'nullable|integer'
        ]);

        return $this->service->create($data);
    }

    public function show(Appointment $appointment)
    {
        return $appointment->load(['patient', 'vet']);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'scheduled_at' => 'date',
            'status' => 'string',
            'notes' => 'nullable',
        ]);

        $appointment->update($data);
        return $appointment;
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->noContent();
    }
}
