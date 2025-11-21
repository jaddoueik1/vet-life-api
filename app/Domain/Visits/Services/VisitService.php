<?php

namespace App\Domain\Visits\Services;

use App\Domain\Visits\Models\Visit;
use App\Domain\Visits\Events\VisitCreated;
use Illuminate\Support\Facades\Event;

class VisitService
{
    public function create(array $data): Visit
    {
        $medications = $data['medication_ids'] ?? [];
        $services = $data['service_ids'] ?? [];
        $equipmentUsed = $data['equipment_used_ids'] ?? [];
        unset($data['medication_ids']);
        unset($data['service_ids']);
        unset($data['equipment_used_ids']);

        $visit = Visit::create($data);

        if (! empty($medications)) {
            $pivotData = collect($medications)
                ->mapWithKeys(fn($medication) => [
                    $medication => ['quantity' => 1],
                ])
                ->toArray();
            $visit->medications()->sync($pivotData);
        }

        if (! empty($services)) {
            $servicePivotData = collect($services)
                ->mapWithKeys(fn($service) => [
                    $service => ['quantity' => 1],
                ])
                ->toArray();
            $visit->services()->sync($servicePivotData);
        }

        if (! empty($equipmentUsed)) {
            $equipmentPivotData = collect($equipmentUsed)
                ->mapWithKeys(fn($equipment) => [
                    $equipment => ['quantity' => 1],
                ])
                ->toArray();
            $visit->equipmentUsed()->sync($equipmentPivotData);
        }

        Event::dispatch(new VisitCreated($visit->toArray()));
        return $visit;
    }
}
