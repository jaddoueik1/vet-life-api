<?php

namespace App\Domain\Patients\Http\Controllers;

use App\Domain\Patients\Models\HealthPlan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HealthPlanController extends Controller
{
    public function index()
    {
        return HealthPlan::with('species')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'species_id' => 'required|exists:species,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'vaccinations' => 'array',
            'vaccinations.*.id' => 'required|exists:vaccinations,id',
            'vaccinations.*.frequency_days' => 'required|integer',
            'vaccinations.*.start_age_weeks' => 'required|integer',
        ]);

        $healthPlan = HealthPlan::create($data);

        if (isset($data['vaccinations'])) {
            $syncData = [];
            foreach ($data['vaccinations'] as $vaccine) {
                $syncData[$vaccine['id']] = [
                    'frequency_days' => $vaccine['frequency_days'],
                    'start_age_weeks' => $vaccine['start_age_weeks'],
                ];
            }
            $healthPlan->vaccinations()->sync($syncData);
        }

        return $healthPlan->load('vaccinations');
    }

    public function show(HealthPlan $healthPlan)
    {
        return $healthPlan->load(['species', 'vaccinations']);
    }

    public function update(Request $request, HealthPlan $healthPlan)
    {
        $data = $request->validate([
            'species_id' => 'exists:species,id',
            'name' => 'string',
            'description' => 'nullable|string',
            'vaccinations' => 'array',
            'vaccinations.*.id' => 'required|exists:vaccinations,id',
            'vaccinations.*.frequency_days' => 'required|integer',
            'vaccinations.*.start_age_weeks' => 'required|integer',
        ]);

        $healthPlan->update($data);

        if (isset($data['vaccinations'])) {
            $syncData = [];
            foreach ($data['vaccinations'] as $vaccine) {
                $syncData[$vaccine['id']] = [
                    'frequency_days' => $vaccine['frequency_days'],
                    'start_age_weeks' => $vaccine['start_age_weeks'],
                ];
            }
            $healthPlan->vaccinations()->sync($syncData);
        }

        return $healthPlan->load('vaccinations');
    }

    public function destroy(HealthPlan $healthPlan)
    {
        $healthPlan->delete();

        return response()->noContent();
    }
}
