<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    protected $signature = 'invoices:send-reminders';
    protected $description = 'Send email reminders for invoices approaching or past due date';

    public function handle()
    {
        $today = Carbon::today();

        // Reminder 3 days BEFORE due date (bypass global scope for background task)
        $upcomingDue = Invoice::withoutGlobalScopes()->with('customer')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereDate('due_date', $today->copy()->addDays(3))
            ->get();

        foreach ($upcomingDue as $invoice) {
            /** @var Invoice $invoice */
            if ($invoice->customer->email) {
                try {
                    Mail::to($invoice->customer->email)->send(
                        new \App\Mail\PaymentReminder($invoice, 'upcoming')
                    );
                    $this->info("  Upcoming reminder sent: {$invoice->invoice_number} → {$invoice->customer->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send reminder for {$invoice->invoice_number}: " . $e->getMessage());
                }
            }
        }

        // Reminder for OVERDUE invoices (1 day after due date) (bypass global scope for background task)
        $overdue = Invoice::withoutGlobalScopes()->with('customer')
            ->where('status', 'overdue')
            ->whereDate('due_date', $today->copy()->subDay())
            ->get();

        foreach ($overdue as $invoice) {
            /** @var Invoice $invoice */
            if ($invoice->customer->email) {
                try {
                    Mail::to($invoice->customer->email)->send(
                        new \App\Mail\PaymentReminder($invoice, 'overdue')
                    );
                    $this->info("  Overdue notice sent: {$invoice->invoice_number} → {$invoice->customer->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send overdue notice for {$invoice->invoice_number}: " . $e->getMessage());
                }
            }
        }

        $this->info("✓ Reminders processed. Upcoming: {$upcomingDue->count()}, Overdue: {$overdue->count()}");

        return Command::SUCCESS;
    }
}
