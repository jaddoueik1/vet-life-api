<?php

namespace App\Domain\Visits\Listeners;

use App\Domain\Invoicing\Services\InvoiceService;
use App\Domain\Visits\Events\VisitCreated;
use App\Domain\Visits\Models\Visit;

class CreateInvoiceForVisit
{
    public function __construct(private InvoiceService $invoiceService)
    {
    }

    public function handle(VisitCreated $event): void
    {
        $visitId = $event->payload['id'] ?? null;

        if ($visitId === null) {
            return;
        }

        $visit = Visit::with('patient.owner', 'medications')->find($visitId);

        if ($visit === null || $visit->patient === null || $visit->patient->owner === null) {
            return;
        }

        $this->invoiceService->create([
            'owner_id' => $visit->patient->owner_id,
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'status' => 'draft',
            'line_items' => [],
        ]);
    }
}
