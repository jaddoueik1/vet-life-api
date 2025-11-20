<?php

namespace App\Domain\Visits\Services;

use App\Domain\Visits\Models\Visit;
use App\Domain\Visits\Events\VisitCreated;
use Illuminate\Support\Facades\Event;

class VisitService
{
    public function create(array $data): Visit
    {
        $visit = Visit::create($data);
        Event::dispatch(new VisitCreated($visit->toArray()));
        return $visit;
    }
}
