<?php

namespace App\Domain\Visits\Services;

use App\Domain\Visits\Models\Visit;
use App\Domain\Visits\Events\VisitCreated;
use Illuminate\Support\Facades\Event;

class VisitService
{
    public function create(array $data): Visit
    {
        $medications = $data['medications'] ?? [];
        $services = $data['services'] ?? [];
        unset($data['medications']);
        unset($data['services']);

        $visit = Visit::create($data);

        if (! empty($medications)) {
            $pivotData = collect($medications)
                ->mapWithKeys(fn ($medication) => [
                    $medication['medication_id'] => ['quantity' => $medication['quantity'] ?? 1],
                ])
                ->toArray();

            $visit->medications()->sync($pivotData);
        }

        if (! empty($services)) {
            $servicePivotData = collect($services)
                ->mapWithKeys(fn ($service) => [
                    $service['service_id'] => ['quantity' => $service['quantity'] ?? 1],
                ])
                ->toArray();

            $visit->services()->sync($servicePivotData);
        }

        Event::dispatch(new VisitCreated($visit->toArray()));
        return $visit;
    }
}
