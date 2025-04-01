<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;

// Public Routes (Registration and Login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Requires Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    // Company CRUD Routes
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::get('/companies/{company}', [CompanyController::class, 'show']);
    Route::put('/companies/{company}', [CompanyController::class, 'update']);
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);  // Get all invoices for the logged-in user
    Route::post('/invoices', [InvoiceController::class, 'store']); // Create a new invoice
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']); // Get a specific invoice
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']); // Update a specific invoice
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']); // Delete an invoice
    Route::post('/invoices/{invoice}/items', [InvoiceItemController::class, 'store']);
    Route::get('/invoices/{invoice}/items', [InvoiceItemController::class, 'index']);
    Route::post('/invoices/{invoice}/items', [InvoiceItemController::class, 'store']);
    Route::get('/invoices/{invoice}/items/{invoiceItem}', [InvoiceItemController::class, 'show']);
    Route::put('/invoices/{invoice}/items/{invoiceItem}', [InvoiceItemController::class, 'update']);
    Route::delete('/invoices/{invoice}/items/{invoiceItem}', [InvoiceItemController::class, 'destroy']);
    Route::get('/invoices/{invoice}/pdf/download', [InvoiceController::class, 'downloadPdf']);
    Route::get('/invoices/{invoice}/pdf/stream', [InvoiceController::class, 'streamPdf']);
});
