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
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('total', 15, 2);
            $table->date('invoice_date');
            $table->integer('invoice_number_ordinal');
            $table->date('due_date'); // Add the due date here
            $table->string('status', 20);
            $table->decimal('tax', 5, 2);
            $table->decimal('discount', 15, 2);
            $table->string('currency', 3);
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
