<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',    // Name of the tax (e.g., VAT, Sales Tax)
        'rate',    // Tax rate (e.g., 18.5 for 18.5%)
    ];

    /**
     * Get the invoices for the tax.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

