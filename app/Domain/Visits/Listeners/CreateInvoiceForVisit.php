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

        $visit = Visit::with('patient.owner', 'medications', 'services')->find($visitId);

        \Log::info('Handling VisitCreated event for visit ID: ' . $visitId);
        \Log::info('Loaded visit: ' . ($visit ? 'found' : 'not found'));
        if ($visit === null || $visit->patient === null || $visit->patient->owner === null) {
            return;
        }

        $medicationLineItems = $visit->medications->map(function ($medication) {
            return [
                'description' => $medication->name,
                'quantity' => $medication->pivot->quantity ?? 1,
                'price' => $medication->price,
            ];
        })->toArray();

        $serviceLineItems = $visit->services->map(function ($service) {
            return [
                'description' => $service->name,
                'quantity' => $service->pivot->quantity ?? 1,
                'price' => $service->price,
            ];
        })->toArray();

        \Log::info('Creating invoice for visit ID: ' . $visit->id);
        \Log::info('Medication Line Items: ' . json_encode($medicationLineItems));
        \Log::info('Service Line Items: ' . json_encode($serviceLineItems));
        $this->invoiceService->create([
            'owner_id' => $visit->patient->owner_id,
            'patient_id' => $visit->patient_id,
            'visit_id' => $visit->id,
            'status' => 'draft',
            'line_items' => array_merge($medicationLineItems, $serviceLineItems),
        ]);
    }
}
