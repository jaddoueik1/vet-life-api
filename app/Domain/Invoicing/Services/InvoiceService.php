<?php

namespace App\Domain\Invoicing\Services;

use App\Domain\Invoicing\Models\Invoice;
use App\Domain\Invoicing\Events\InvoiceCreated;
use Illuminate\Support\Facades\Event;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        $invoice = Invoice::create($data);
        Event::dispatch(new InvoiceCreated($invoice->toArray()));
        return $invoice;
    }
}
