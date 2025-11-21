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
        unset($data['medications']);

        $visit = Visit::create($data);

        if (! empty($medications)) {
            $pivotData = collect($medications)
                ->mapWithKeys(fn ($medication) => [
                    $medication['medication_id'] => ['quantity' => $medication['quantity'] ?? 1],
                ])
                ->toArray();

            $visit->medications()->sync($pivotData);
        }

        Event::dispatch(new VisitCreated($visit->toArray()));
        return $visit;
    }
}
