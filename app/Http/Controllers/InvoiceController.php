<?php

namespace App\Http\Controllers;

use App\Filters\InvoiceFilter;
use App\Models\Invoice;
use App\Rules\OwnsCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Invoice::where('user_id', Auth::id());

        $filter = new InvoiceFilter($request);
        $query = $filter->apply($query);

        return response()->json($query->paginate());
    }

    /**
     * Display the specified invoice.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);

        return response()->json($invoice);
    }

    /**
     * Update the specified invoice in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Invoice $invoice)
    {
        Gate::authorize('update', $invoice);

        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255|unique:invoices,invoice_number,' . $invoice->id,
            'company_id' => ['required', 'exists:companies,id', new OwnsCompany],
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
        ]);

        $invoice->update($validated);

        return response()->json($invoice);
    }

    /**
     * Remove the specified invoice from storage.
     *
     * @param \App\Models\Invoice $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete', $invoice);

        $invoice->delete();

        return response()->json(null, 204);
    }

    /**
     * Generate and download PDF for the specified invoice.
     */
    public function downloadPdf(Invoice $invoice)
    {
        // Load the relationships needed for the PDF
        $invoice->load(['company', 'items']);

        // Generate PDF
        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $invoice
        ]);

        // Return the PDF for download
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Generate and stream PDF for the specified invoice.
     */
    public function streamPdf(Invoice $invoice)
    {
        // Load the relationships needed for the PDF
        $invoice->load(['company', 'items']);

        // Generate PDF
        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $invoice
        ]);

        // Return the PDF for streaming
        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}
