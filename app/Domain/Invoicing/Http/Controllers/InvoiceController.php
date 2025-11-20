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
            'number' => 'required',
            'owner_id' => 'required|integer',
            'status' => 'required',
            'total' => 'required|numeric'
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
