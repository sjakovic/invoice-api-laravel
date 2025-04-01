<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;

class InvoicePolicy
{
    /**
     * Determine if the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice)
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can create invoices.
     */
    public function create(User $user)
    {
        // Logic for whether the user can create an invoice
        return true;
    }

    /**
     * Determine if the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice)
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice)
    {
        return $user->id === $invoice->user_id;
    }
}
