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
            'visit_id' => 'nullable|integer|exists:visits,id',
            'status' => 'required|string',
            'line_items' => 'nullable|array',
            'line_items.*.description' => 'required_with:line_items|string',
            'line_items.*.quantity' => 'required_with:line_items|integer|min:1',
            'line_items.*.unit_price' => 'required_with:line_items|numeric|min:0',
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

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        $paidTotal = (float) $invoice->payments()->sum('amount');
        $remaining = max(0, (float) $invoice->total - $paidTotal);

        if ($data['amount'] > $remaining) {
            return response()->json([
                'message' => 'Payment exceeds remaining balance.',
            ], 422);
        }

        $payment = $invoice->payments()->create([
            'amount' => $data['amount'],
            'method' => $data['method'] ?? null,
            'paid_at' => $data['paid_at'] ?? now(),
        ]);

        if (($remaining - $data['amount']) <= 0.0) {
            $invoice->update(['status' => 'paid']);
        } elseif ($invoice->status === 'sent') {
            $invoice->update(['status' => 'partial']);
        }

        return $payment->load('invoice');
    }
}
