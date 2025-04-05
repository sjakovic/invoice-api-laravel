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
use App\Http\Requests\StoreInvoiceRequest;
use Illuminate\Http\JsonResponse;

/**
 * @group Invoice Management
 * 
 * APIs for managing invoices, including creation, retrieval, updates, and PDF generation.
 */
class InvoiceController extends Controller
{
    /**
     * List Invoices
     * 
     * Get a paginated list of invoices for the authenticated user.
     * 
     * @queryParam page int The page number for pagination. Example: 1
     * @queryParam per_page int Number of items per page. Example: 15
     * @queryParam status string Filter by invoice status (paid, pending, overdue). Example: pending
     * @queryParam from_date date Filter invoices from this date. Example: 2024-01-01
     * @queryParam to_date date Filter invoices until this date. Example: 2024-12-31
     * @queryParam company_id int Filter by company ID. Example: 1
     * @queryParam search string Search in invoice number or company names. Example: INV-2024
     * 
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "invoice_number": "INV-202403-1-0001",
     *       "amount": 1000.00,
     *       "total": 1200.00,
     *       "status": "pending",
     *       "invoice_date": "2024-03-15",
     *       "due_date": "2024-04-15",
     *       "issuer_company_name": "Company Name",
     *       "client_company_name": "Client Name",
     *       "items": [...]
     *     }
     *   ],
     *   "current_page": 1,
     *   "per_page": 15,
     *   "total": 100
     * }
     */
    public function index(Request $request)
    {
        $query = Invoice::where('user_id', Auth::id());

        $filter = new InvoiceFilter($request);
        $query = $filter->apply($query);

        return response()->json($query->paginate());
    }

    /**
     * Get Invoice Details
     * 
     * Retrieve detailed information about a specific invoice.
     * 
     * @urlParam invoice Invoice The invoice ID or invoice number.
     * 
     * @response 200 {
     *   "id": 1,
     *   "invoice_number": "INV-202403-1-0001",
     *   "amount": 1000.00,
     *   "total": 1200.00,
     *   "status": "pending",
     *   "invoice_date": "2024-03-15",
     *   "due_date": "2024-04-15",
     *   "issuer_company_name": "Company Name",
     *   "issuer_address": "123 Main St",
     *   "issuer_city": "New York",
     *   "issuer_country": "USA",
     *   "issuer_tax_number": "TAX-1234",
     *   "client_company_name": "Client Name",
     *   "client_address": "456 Client St",
     *   "client_city": "London",
     *   "client_country": "UK",
     *   "client_tax_number": "CLIENT-TAX-1234",
     *   "items": [...]
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "Invoice not found."
     * }
     */
    public function show(Invoice $invoice)
    {
        Gate::authorize('view', $invoice);

        return response()->json($invoice);
    }

    /**
     * Create Invoice
     * 
     * Create a new invoice with items.
     * 
     * @bodyParam company_id int required The ID of the company issuing the invoice. Example: 1
     * @bodyParam issuer_company_name string required The name of the company issuing the invoice. Example: "Company Name"
     * @bodyParam issuer_address string required The address of the issuing company. Example: "123 Main St"
     * @bodyParam issuer_street_number string The street number of the issuing company. Example: "123"
     * @bodyParam issuer_city string The city of the issuing company. Example: "New York"
     * @bodyParam issuer_email string The email of the issuing company. Example: "company@example.com"
     * @bodyParam issuer_country string The country of the issuing company. Example: "USA"
     * @bodyParam issuer_company_number string The company registration number. Example: "COMP-1234"
     * @bodyParam issuer_tax_number string The tax number of the issuing company. Example: "TAX-1234"
     * @bodyParam issuer_contact_person string The contact person at the issuing company. Example: "John Doe"
     * @bodyParam client_company_name string required The name of the client company. Example: "Client Name"
     * @bodyParam client_address string required The address of the client company. Example: "456 Client St"
     * @bodyParam client_street_number string The street number of the client company. Example: "456"
     * @bodyParam client_city string The city of the client company. Example: "London"
     * @bodyParam client_email string The email of the client company. Example: "client@example.com"
     * @bodyParam client_country string The country of the client company. Example: "UK"
     * @bodyParam client_company_number string The company registration number of the client. Example: "CLIENT-1234"
     * @bodyParam client_tax_number string The tax number of the client company. Example: "CLIENT-TAX-1234"
     * @bodyParam client_contact_person string The contact person at the client company. Example: "Jane Doe"
     * @bodyParam invoice_date date required The date of the invoice. Example: "2024-03-15"
     * @bodyParam due_date date required The due date of the invoice. Example: "2024-04-15"
     * @bodyParam tax numeric required The tax percentage. Example: 20
     * @bodyParam discount numeric required The discount amount. Example: 0
     * @bodyParam currency string required The currency code (3 letters). Example: "USD"
     * @bodyParam items array required Array of invoice items. Example: [{"description": "Item 1", "quantity": 2, "price": 100}]
     * @bodyParam items.*.description string required Description of the item. Example: "Item 1"
     * @bodyParam items.*.quantity numeric required Quantity of the item. Example: 2
     * @bodyParam items.*.price numeric required Price per unit. Example: 100
     * 
     * @response 201 {
     *   "message": "Invoice created successfully",
     *   "data": {
     *     "id": 1,
     *     "invoice_number": "INV-202403-1-0001",
     *     "amount": 1000.00,
     *     "total": 1200.00,
     *     "status": "pending",
     *     "invoice_date": "2024-03-15",
     *     "due_date": "2024-04-15",
     *     "issuer_company_name": "Company Name",
     *     "client_company_name": "Client Name",
     *     "items": [...]
     *   }
     * }
     * 
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "company_id": ["The selected company id is invalid."],
     *     "issuer_company_name": ["The issuer company name field is required."],
     *     ...
     *   }
     * }
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Calculate invoice totals
            $items = collect($request->items);
            $amount = $items->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
            $total = $amount + ($amount * ($request->tax / 100)) - $request->discount;

            // Get the next invoice number ordinal
            $latestInvoice = Invoice::where('company_id', $request->company_id)
                ->whereYear('invoice_date', $request->invoice_date->year)
                ->whereMonth('invoice_date', $request->invoice_date->month)
                ->orderBy('invoice_number_ordinal', 'desc')
                ->first();

            $nextOrdinal = $latestInvoice ? $latestInvoice->invoice_number_ordinal + 1 : 1;

            // Create the invoice
            $invoice = Invoice::create([
                'user_id' => auth()->id(),
                'company_id' => $request->company_id,
                // Issuer information
                'issuer_company_name' => $request->issuer_company_name,
                'issuer_address' => $request->issuer_address,
                'issuer_street_number' => $request->issuer_street_number,
                'issuer_city' => $request->issuer_city,
                'issuer_email' => $request->issuer_email,
                'issuer_country' => $request->issuer_country,
                'issuer_company_number' => $request->issuer_company_number,
                'issuer_tax_number' => $request->issuer_tax_number,
                'issuer_contact_person' => $request->issuer_contact_person,
                // Client information
                'client_company_name' => $request->client_company_name,
                'client_address' => $request->client_address,
                'client_street_number' => $request->client_street_number,
                'client_city' => $request->client_city,
                'client_email' => $request->client_email,
                'client_country' => $request->client_country,
                'client_company_number' => $request->client_company_number,
                'client_tax_number' => $request->client_tax_number,
                'client_contact_person' => $request->client_contact_person,
                // Invoice details
                'invoice_number' => $this->generateInvoiceNumber($request->company_id, $request->invoice_date),
                'invoice_number_ordinal' => $nextOrdinal,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'amount' => $amount,
                'total' => $total,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'currency' => $request->currency,
                'status' => 'pending',
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice created successfully',
                'data' => $invoice->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Invoice
     * 
     * Update an existing invoice's details.
     * 
     * @urlParam invoice Invoice The invoice ID or invoice number.
     * 
     * @bodyParam invoice_number string required The unique invoice number. Example: "INV-202403-1-0001"
     * @bodyParam company_id int required The ID of the company. Example: 1
     * @bodyParam amount numeric required The invoice amount. Example: 1000.00
     * @bodyParam tax numeric required The tax percentage. Example: 20
     * @bodyParam total numeric required The total amount including tax. Example: 1200.00
     * @bodyParam invoice_date date required The date of the invoice. Example: "2024-03-15"
     * @bodyParam due_date date required The due date of the invoice. Example: "2024-04-15"
     * 
     * @response 200 {
     *   "id": 1,
     *   "invoice_number": "INV-202403-1-0001",
     *   "amount": 1000.00,
     *   "total": 1200.00,
     *   "status": "pending",
     *   "invoice_date": "2024-03-15",
     *   "due_date": "2024-04-15"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "invoice_number": ["The invoice number has already been taken."],
     *     ...
     *   }
     * }
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
     * Delete Invoice
     * 
     * Delete an existing invoice.
     * 
     * @urlParam invoice Invoice The invoice ID or invoice number.
     * 
     * @response 204
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     */
    public function destroy(Invoice $invoice)
    {
        Gate::authorize('delete', $invoice);

        $invoice->delete();

        return response()->json(null, 204);
    }

    /**
     * Download Invoice PDF
     * 
     * Generate and download a PDF version of the invoice.
     * 
     * @urlParam invoice Invoice The invoice ID or invoice number.
     * 
     * @response 200 {
     *   "file": "invoice-INV-202403-1-0001.pdf"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
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
     * Stream Invoice PDF
     * 
     * Generate and stream a PDF version of the invoice in the browser.
     * 
     * @urlParam invoice Invoice The invoice ID or invoice number.
     * 
     * @response 200 {
     *   "file": "invoice-INV-202403-1-0001.pdf"
     * }
     * 
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
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

    /**
     * Generate a unique invoice number.
     * Format: INV-YYYYMM-COMPANYID-XXXX (sequential)
     */
    private function generateInvoiceNumber(int $companyId, string $date): string
    {
        $yearMonth = date('Ym', strtotime($date));
        return "INV-{$yearMonth}-{$companyId}-" . str_pad($nextOrdinal, 4, '0', STR_PAD_LEFT);
    }
}
