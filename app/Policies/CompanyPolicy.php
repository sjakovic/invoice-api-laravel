<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;

class CompanyPolicy
{
    /**
     * Determine if the user can view the company.
     */
    public function view(User $user, Company $company)
    {
        return $user->id === $company->user_id; // Only allow viewing companies that belong to the authenticated user
    }

    /**
     * Determine if the user can create companies.
     */
    public function create(User $user)
    {
        return true; // Allow creation of companies for any authenticated user
    }

    /**
     * Determine if the user can update the company.
     */
    public function update(User $user, Company $company)
    {
        return $user->id === $company->user_id; // Only allow updating companies that belong to the authenticated user
    }

    /**
     * Determine if the user can delete the company.
     */
    public function delete(User $user, Company $company)
    {
        return $user->id === $company->user_id; // Only allow deleting companies that belong to the authenticated user
    }
}
