<?php

namespace App\Domain\Invoicing\Services;

use App\Domain\Invoicing\Models\Invoice;
use App\Domain\Invoicing\Events\InvoiceCreated;
use App\Domain\Visits\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        $lineItems = $data['line_items'] ?? [];

        if (! empty($data['visit_id'])) {
            $visit = Visit::with('medications')->find($data['visit_id']);

            if ($visit) {
                $medicationLineItems = $visit->medications->map(function ($medication) {
                    return [
                        'description' => $medication->name,
                        'quantity' => $medication->pivot->quantity ?? 1,
                        'unit_price' => $medication->unit_price,
                    ];
                })->toArray();

                $lineItems = array_merge($lineItems, $medicationLineItems);
            }
        }

        return DB::transaction(function () use ($data, $lineItems) {
            $payload = [
                'owner_id' => $data['owner_id'],
                'patient_id' => $data['patient_id'],
                'visit_id' => $data['visit_id'] ?? null,
                'status' => $data['status'],
                'number' => 'INV-' . Str::upper(Str::random(8)),
                'total' => round(
                    collect($lineItems)->reduce(
                        fn (float $carry, array $item) => $carry + ($item['unit_price'] * $item['quantity']),
                        0.0
                    ),
                    2
                ),
            ];

            $invoice = Invoice::create($payload);

            $invoice->lineItems()->createMany(
                collect($lineItems)->map(function (array $item) {
                    return [
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'price' => $item['unit_price'],
                    ];
                })->toArray()
            );

            Event::dispatch(new InvoiceCreated($invoice->toArray()));

            return $invoice->load('lineItems');
        });
    }
}
