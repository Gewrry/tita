<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateOverdueInvoices extends Command
{
    protected $signature = 'invoices:check-overdue';
    protected $description = 'Check for overdue invoices and update their status, apply penalties';

    public function handle()
    {
        $today = Carbon::today();

        // Find invoices that are past due and not paid (bypass global scope for background task)
        $overdueInvoices = Invoice::withoutGlobalScopes()
            ->whereIn('status', ['unpaid', 'partial'])
            ->where('due_date', '<', $today)
            ->get();

        $count = 0;

        foreach ($overdueInvoices as $invoice) {
            /** @var Invoice $invoice */
            $oldStatus = $invoice->status;
            $invoice->status = 'overdue';

            // Auto-apply penalty if configured and not yet applied
            if ($invoice->penalty_type !== 'none' && $invoice->penalty_amount == 0) {
                $invoice->applyPenalty();
                $this->info("  Penalty applied to {$invoice->invoice_number}: ₱" . number_format($invoice->penalty_amount, 2));
            }

            $invoice->save();
            $count++;

            $this->info("  {$invoice->invoice_number}: {$oldStatus} → overdue (Due: {$invoice->due_date->format('M d, Y')})");
        }

        $this->info("✓ {$count} invoice(s) marked as overdue.");

        return Command::SUCCESS;
    }
}
