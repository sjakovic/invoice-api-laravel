<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'issuer_company_name',
        'issuer_address',
        'issuer_street_number',
        'issuer_city',
        'issuer_email',
        'issuer_country',
        'issuer_company_number',
        'issuer_tax_number',
        'issuer_contact_person',
        'client_company_name',
        'client_address',
        'client_street_number',
        'client_city',
        'client_email',
        'client_country',
        'client_company_number',
        'client_tax_number',
        'client_contact_person',
        'invoice_number',
        'amount',
        'total',
        'invoice_date',
        'invoice_number_ordinal',
        'due_date',
        'status',
        'tax',
        'discount',
        'currency'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'total' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotal()
    {
        $subtotal = 0;
        $totalTax = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->total;
            $totalTax += ($item->unit_price * $item->quantity) * ($item->tax_rate / 100);
        }

        return $subtotal + $totalTax;
    }


}
