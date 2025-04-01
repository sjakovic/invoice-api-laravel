<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $statuses = ['paid', 'pending', 'overdue'];

        $user = User::create([
            'name' => $faker->name,
            'email' => 'sjakovic+invoices-user@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // Create 3 companies for the user
        for ($i = 0; $i < 3; $i++) {
            $company = Company::create([
                'user_id' => $user->id,
                'company_name' => $faker->company,
                'address' => $faker->streetAddress,
                'street_number' => $faker->buildingNumber,
                'city' => $faker->city,
                'email' => $faker->companyEmail,
                'country' => $faker->country,
                'company_number' => $faker->numerify('COMP-####'),
                'tax_number' => $faker->numerify('TAX-####'),
                'contact_person' => $faker->name,
            ]);

            // Create 10-15 invoices for each company
            $numberOfInvoices = $faker->numberBetween(10, 15);
            for ($j = 0; $j < $numberOfInvoices; $j++) {
                $invoiceDate = $faker->dateTimeBetween('-1 year', 'now');
                $dueDate = $faker->dateTimeBetween($invoiceDate, '+30 days');
                $amount = $faker->randomFloat(2, 100, 10000);
                $tax = $faker->randomFloat(2, 0, 25);
                $discount = $faker->randomFloat(2, 0, 100);
                $total = $amount + ($amount * ($tax / 100)) - $discount;

                $invoice = Invoice::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'invoice_number' => $this->generateInvoiceNumber($company),
                    'amount' => $amount,
                    'total' => $total,
                    'invoice_date' => $invoiceDate,
                    'invoice_number_ordinal' => $j + 1,
                    'due_date' => $dueDate,
                    'status' => $faker->randomElement($statuses),
                    'tax' => $tax,
                    'discount' => $discount,
                    'currency' => 'USD'
                ]);

                // Create 1-5 items for each invoice
                $numberOfItems = $faker->numberBetween(1, 5);
                for ($k = 0; $k < $numberOfItems; $k++) {
                    $quantity = $faker->randomFloat(2, 1, 10);
                    $price = $faker->randomFloat(2, 10, 1000);
                    $itemTotal = $quantity * $price;

                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $faker->sentence(3),
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $itemTotal,
                    ]);
                }
            }
        }
    }

    /**
     * Generate a unique invoice number.
     * Format: INV-YYYYMM-COMPANYID-XXXX (sequential)
     */
    private function generateInvoiceNumber(Company $company): string
    {
        $yearMonth = now()->format('Ym');
        $latestInvoice = Invoice::where('company_id', $company->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $latestInvoice ? intval(substr($latestInvoice->invoice_number, -4)) + 1 : 1;
        return "INV-{$yearMonth}-{$company->id}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
