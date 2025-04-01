<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class InvoiceFilter
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // 🔹 Filtering
        if ($this->request->filled('company_id')) {
            $query->where('company_id', $this->request->company_id);
        }

        // 🔹 Sorting
        if ($this->request->filled('sort_by') && in_array($this->request->sort_by, ['created_at'])) {
            $direction = $this->request->get('sort_direction', 'asc') === 'desc' ? 'desc' : 'asc';
            $query->orderBy($this->request->sort_by, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }
}
