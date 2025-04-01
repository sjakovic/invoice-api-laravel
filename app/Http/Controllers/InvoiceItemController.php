<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceItemController extends Controller
{
    /**
     * Display a listing of the invoice items.
     */
    public function index(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);

        $items = $invoice->items()->paginate(10);

        return response()->json($items);
    }

    /**
     * Store multiple new invoice items.
     */
    public function store(Request $request, Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0|max:100', // Valid tax %
            'items.*.total' => 'required|numeric|min:0',
        ]);

        foreach ($validated['items'] as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                ...$item
            ]);
        }

        return response()->json(['message' => 'Invoice items added successfully'], 201);
    }

    /**
     * Display a specific invoice item.
     */
    public function show(Invoice $invoice, InvoiceItem $invoiceItem)
    {
        Gate::authorize('view', $invoiceItem);

        return response()->json($invoiceItem);
    }

    /**
     * Update the specified invoice item.
     */
    public function update(Request $request, Invoice $invoice, InvoiceItem $invoiceItem)
    {
        Gate::authorize('update', $invoiceItem);

        $validated = $request->validate([
            'description' => 'sometimes|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
            'tax_rate' => 'sometimes|numeric|min:0|max:100',
            'total' => 'sometimes|numeric|min:0',
        ]);

        $invoiceItem->update($validated);

        return response()->json($invoiceItem);
    }

    /**
     * Remove the specified invoice item.
     */
    public function destroy(Invoice $invoice, InvoiceItem $invoiceItem)
    {
        Gate::authorize('delete', $invoiceItem);

        $invoiceItem->delete();

        return response()->json(['message' => 'Invoice item deleted successfully'], 204);
    }
}
