<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@tita.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create customers
        $customers = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@example.com', 'phone' => '09171234567', 'address' => 'Manila, Philippines'],
            ['name' => 'Maria Santos', 'email' => 'maria@example.com', 'phone' => '09181234567', 'address' => 'Cebu City, Philippines'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro@example.com', 'phone' => '09191234567', 'address' => 'Davao City, Philippines'],
            ['name' => 'Ana Garcia', 'email' => 'ana@example.com', 'phone' => '09201234567', 'address' => 'Quezon City, Philippines'],
            ['name' => 'Carlos Mendoza', 'email' => 'carlos@example.com', 'phone' => '09211234567', 'address' => 'Makati City, Philippines'],
        ];

        foreach ($customers as $c) {
            Customer::create($c);
        }

        // Create invoices with items
        $items = [
            ['Web Development Service', 1, 25000],
            ['Logo Design', 1, 5000],
            ['Monthly Hosting', 12, 500],
            ['SEO Package', 1, 8000],
            ['Social Media Management', 3, 3500],
            ['Content Writing', 5, 1500],
            ['IT Support', 1, 10000],
            ['Training Session', 2, 4000],
        ];

        for ($i = 1; $i <= 5; $i++) {
            $issueDate = Carbon::now()->subDays(rand(1, 60));
            $invoice = Invoice::create([
                'customer_id' => $i,
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'issue_date' => $issueDate,
                'due_date' => $issueDate->copy()->addDays(30),
                'penalty_type' => 'none',
                'penalty_value' => 0,
                'status' => 'unpaid',
            ]);

            // Add 2-3 random items
            $numItems = rand(2, 3);
            $selected = array_rand($items, $numItems);
            if (!is_array($selected)) $selected = [$selected];

            foreach ($selected as $idx) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $items[$idx][0],
                    'quantity' => $items[$idx][1],
                    'price' => $items[$idx][2],
                    'amount' => $items[$idx][1] * $items[$idx][2],
                ]);
            }

            $invoice->recalculateTotal();
        }

        // Create some payments (partially paid invoices)
        $invoices = Invoice::all();
        foreach ($invoices->take(3) as $invoice) {
            $payAmount = $invoice->total_amount * (rand(30, 80) / 100);
            Payment::create([
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'amount' => round($payAmount, 2),
                'payment_method' => ['cash', 'bank_transfer', 'gcash'][rand(0, 2)],
                'payment_date' => Carbon::now()->subDays(rand(1, 15)),
                'reference_number' => 'REF-' . strtoupper(substr(md5(rand()), 0, 8)),
            ]);
        }

        // Fully pay one invoice
        $fullPaidInvoice = $invoices->skip(3)->first();
        if ($fullPaidInvoice) {
            Payment::create([
                'invoice_id' => $fullPaidInvoice->id,
                'customer_id' => $fullPaidInvoice->customer_id,
                'amount' => $fullPaidInvoice->total_amount,
                'payment_method' => 'gcash',
                'payment_date' => Carbon::now()->subDays(5),
                'reference_number' => 'REF-FULLPAID01',
            ]);
        }

        // Create expenses
        $expenseData = [
            ['Office Supplies - Paper, Ink', 'supplies', 2500],
            ['Monthly Office Rent', 'rent', 15000],
            ['Employee Salary - March', 'salary', 25000],
            ['Electricity Bill', 'utilities', 3500],
            ['Gas/Transportation', 'transportation', 1500],
            ['Team Lunch', 'food', 800],
            ['Internet Bill', 'utilities', 1800],
            ['Office Cleaning Service', 'other', 2000],
        ];

        foreach ($expenseData as $exp) {
            Expense::create([
                'description' => $exp[0],
                'category' => $exp[1],
                'amount' => $exp[2],
                'expense_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
