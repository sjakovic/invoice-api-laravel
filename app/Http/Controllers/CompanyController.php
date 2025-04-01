<?php

namespace App\Http\Controllers;

use App\Filters\CompanyFilter;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index(Request $request)
    {
        $query = Company::where('user_id', auth()->id());

        $filter = new CompanyFilter($request);
        $query = $filter->apply($query);

        return response()->json($query->paginate());
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Company::class);

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'tax_number' => 'required|string|max:20',
            'email' => 'nullable|email',
            'street_number' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);


        // Create the company with the authenticated user
        $company = Company::create([
            'user_id' => auth()->id(),
            ...$validated
        ]);

        return response()->json($company, 201);
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        Gate::authorize('view', $company);

        return response()->json($company);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        Gate::authorize('update', $company);

        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'tax_number' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'street_number' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $company->update($validated);

        return response()->json($company);
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        Gate::authorize('delete', $company);

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully'], 204);
    }
}
