<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $type;

    public function __construct(Invoice $invoice, string $type = 'upcoming')
    {
        $this->invoice = $invoice;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subject = $this->type === 'overdue'
            ? "⚠️ Overdue Notice: Invoice {$this->invoice->invoice_number}"
            : "📋 Payment Reminder: Invoice {$this->invoice->invoice_number}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-reminder');
    }
}
