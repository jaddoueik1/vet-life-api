<?php

namespace App\Domain\Invoicing\Http\Controllers;

use App\Domain\Invoicing\Models\Invoice;
use App\Domain\Invoicing\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $service)
    {
    }

    public function index()
    {
        return Invoice::with('lineItems')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'owner_id' => 'required|integer|exists:owners,id',
            'status' => 'required|string',
            'line_items' => 'required|array|min:1',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|integer|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return $this->service->create($data);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load('lineItems');
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());
        return $invoice;
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->noContent();
    }
}
