<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceItemPolicy
{
    /**
     * Determine if the user can view the invoice item.
     */
    public function view(User $user, InvoiceItem $invoiceItem)
    {
        return $user->id === $invoiceItem->invoice->user_id; // Only allow viewing items from invoices belonging to the user
    }

    /**
     * Determine if the user can create an invoice item for a specific invoice.
     */
    public function create(User $user, Invoice $invoice)
    {
        return $user->id === $invoice->user_id; // Only allow creating invoice items for invoices belonging to the user
    }

    /**
     * Determine if the user can update the invoice item.
     */
    public function update(User $user, InvoiceItem $invoiceItem)
    {
        return $user->id === $invoiceItem->invoice->user_id; // Only allow updating items from invoices belonging to the user
    }

    /**
     * Determine if the user can delete the invoice item.
     */
    public function delete(User $user, InvoiceItem $invoiceItem)
    {
        return $user->id === $invoiceItem->invoice->user_id; // Only allow deleting items from invoices belonging to the user
    }
}
