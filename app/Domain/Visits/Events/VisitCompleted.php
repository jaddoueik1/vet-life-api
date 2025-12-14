<?php

namespace App\Domain\Visits\Events;

use App\Domain\Visits\Models\Visit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Visit $visit;

    /**
     * Create a new event instance.
     */
    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }
}
