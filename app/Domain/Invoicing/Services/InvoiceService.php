<?php

namespace App\Domain\Invoicing\Services;

use App\Domain\Invoicing\Models\Invoice;
use App\Domain\Invoicing\Events\InvoiceCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        $lineItems = $data['line_items'];

        return DB::transaction(function () use ($data, $lineItems) {
            $payload = [
                'owner_id' => $data['owner_id'],
                'patient_id' => $data['patient_id'],
                'status' => $data['status'],
                'number' => count($lineItems),
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
