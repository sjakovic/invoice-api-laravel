<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CompanyFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // 🔹 Filtering
        if ($this->request->filled('company_name')) {
            $query->where('company_name', 'like', '%' . $this->request->company_name . '%');
        }

        if ($this->request->filled('city')) {
            $query->where('city', 'like', '%' . $this->request->city . '%');
        }

        if ($this->request->filled('country')) {
            $query->where('country', $this->request->country);
        }

        if ($this->request->filled('tax_number')) {
            $query->where('tax_number', $this->request->tax_number);
        }

        // 🔹 Sorting
        if ($this->request->filled('sort_by') && in_array($this->request->sort_by, ['company_name', 'city', 'country', 'created_at'])) {
            $direction = $this->request->get('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $query->orderBy($this->request->sort_by, $direction);
        } else {
            $query->orderBy('created_at', 'desc'); // Default sorting
        }

        return $query;
    }
}
