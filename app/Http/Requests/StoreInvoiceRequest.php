<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            // Issuer (company) information
            'issuer_company_name' => 'required|string|max:255',
            'issuer_address' => 'required|string|max:255',
            'issuer_street_number' => 'nullable|string|max:50',
            'issuer_city' => 'nullable|string|max:100',
            'issuer_email' => 'nullable|email|max:255',
            'issuer_country' => 'nullable|string|max:100',
            'issuer_company_number' => 'nullable|string|max:50',
            'issuer_tax_number' => 'nullable|string|max:50',
            'issuer_contact_person' => 'nullable|string|max:255',
            // Client information
            'client_company_name' => 'required|string|max:255',
            'client_address' => 'required|string|max:255',
            'client_street_number' => 'nullable|string|max:50',
            'client_city' => 'nullable|string|max:100',
            'client_email' => 'nullable|email|max:255',
            'client_country' => 'nullable|string|max:100',
            'client_company_number' => 'nullable|string|max:50',
            'client_tax_number' => 'nullable|string|max:50',
            'client_contact_person' => 'nullable|string|max:255',
            // Invoice details
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'tax' => 'required|numeric|min:0|max:100',
            'discount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            // Invoice items
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }
} 