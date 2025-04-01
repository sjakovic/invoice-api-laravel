<?php

namespace App\Providers;

use App\Policies\CompanyPolicy;
use App\Policies\InvoiceItemPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('view-company', [CompanyPolicy::class, 'view']);
        Gate::define('create-company', [CompanyPolicy::class, 'create']);
        Gate::define('update-company', [CompanyPolicy::class, 'update']);
        Gate::define('delete-company', [CompanyPolicy::class, 'delete']);

        Gate::define('view-invoice', [InvoicePolicy::class, 'view']);
        Gate::define('create-invoice', [InvoicePolicy::class, 'create']);
        Gate::define('update-invoice', [InvoicePolicy::class, 'update']);
        Gate::define('delete-invoice', [InvoicePolicy::class, 'delete']);

        Gate::define('view-invoice-item', [InvoiceItemPolicy::class, 'view']);
        Gate::define('create-invoice-item', [InvoiceItemPolicy::class, 'create']);
        Gate::define('update-invoice-item', [InvoiceItemPolicy::class, 'update']);
        Gate::define('delete-invoice-item', [InvoiceItemPolicy::class, 'delete']);
    }
}
