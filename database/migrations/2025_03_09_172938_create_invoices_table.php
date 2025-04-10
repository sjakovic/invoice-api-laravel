<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // To link the invoice to the user
            $table->unsignedBigInteger('company_id'); // Link to company
            // Issuer (company) information (stored at the time of invoice creation)
            $table->string('issuer_company_name');
            $table->string('issuer_address');
            $table->string('issuer_street_number')->nullable();
            $table->string('issuer_city')->nullable();
            $table->string('issuer_email')->nullable();
            $table->string('issuer_country')->nullable();
            $table->string('issuer_company_number')->nullable();
            $table->string('issuer_tax_number')->nullable();
            $table->string('issuer_contact_person')->nullable();
            // Client information (stored at the time of invoice creation)
            $table->string('client_company_name');
            $table->string('client_address');
            $table->string('client_street_number')->nullable();
            $table->string('client_city')->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_country')->nullable();
            $table->string('client_company_number')->nullable();
            $table->string('client_tax_number')->nullable();
            $table->string('client_contact_person')->nullable();
            // Invoice details
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('invoice_date');
            $table->integer('invoice_number_ordinal');
            $table->date('due_date');
            $table->string('status', 20);
            $table->decimal('tax', 5, 2);
            $table->decimal('discount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->unique(['user_id', 'invoice_date', 'invoice_number_ordinal']);
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
