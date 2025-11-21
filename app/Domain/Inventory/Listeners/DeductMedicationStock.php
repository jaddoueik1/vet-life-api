<?php

namespace App\Domain\Inventory\Listeners;

use App\Domain\Inventory\Events\MedicationUsed;
use App\Domain\Medications\Models\Medication;

class DeductMedicationStock
{
    public function handle(MedicationUsed $event): void
    {
        $payload = $event->payload;
        $medicationId = $payload['medication_id'] ?? null;
        $quantity = (int) ($payload['quantity'] ?? 0);

        if ($medicationId === null || $quantity <= 0) {
            return;
        }

        $medication = Medication::find($medicationId);

        if ($medication === null) {
            return;
        }

        $newStock = max(0, ($medication->current_stock ?? 0) - $quantity);
        $medication->update(['current_stock' => $newStock]);
    }
}
