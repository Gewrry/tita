<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 30px; }

        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 3px solid #10b981; padding-bottom: 20px; }
        .brand h1 { font-size: 28px; color: #10b981; margin-bottom: 2px; }
        .brand p { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; }

        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 22px; color: #334155; text-transform: uppercase; }
        .invoice-title .inv-number { font-size: 14px; color: #10b981; font-weight: 600; margin-top: 4px; }

        .meta { margin-bottom: 25px; }
        .meta table { width: 100%; }
        .meta td { vertical-align: top; padding: 4px 0; }
        .meta .label { color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .meta .value { font-size: 12px; font-weight: 600; color: #1e293b; }

        .status-badge {
            display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 10px;
            font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
        }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-partial { background: #fef3c7; color: #d97706; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .status-unpaid { background: #e2e8f0; color: #64748b; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f1f5f9; color: #475569; font-size: 10px; text-transform: uppercase;
                          letter-spacing: 1px; padding: 10px 12px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        .items-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }

        .totals { float: right; width: 280px; margin-top: 10px; }
        .totals table { width: 100%; }
        .totals td { padding: 6px 0; font-size: 12px; }
        .totals .label { color: #64748b; }
        .totals .value { text-align: right; font-weight: 600; }
        .totals .grand-total td { border-top: 2px solid #10b981; padding-top: 10px; font-size: 16px; font-weight: 700; color: #10b981; }

        .payment-history { margin-top: 40px; clear: both; }
        .payment-history h3 { font-size: 13px; color: #475569; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }

        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 10px; }
        .notes { margin-top: 20px; padding: 12px; background: #f8fafc; border-radius: 6px; font-size: 11px; color: #64748b; clear: both; }
    </style>
</head>
<body>
    <!-- Header -->
    <table style="width:100%; margin-bottom: 25px; border-bottom: 3px solid #10b981; padding-bottom: 15px;">
        <tr>
            <td style="width:50%;">
                <h1 style="font-size: 28px; color: #10b981; margin: 0;">TITA</h1>
                <p style="font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin: 0;">Finance System</p>
            </td>
            <td style="width:50%; text-align: right;">
                <h2 style="font-size: 20px; color: #334155; text-transform: uppercase; margin: 0;">INVOICE</h2>
                <p style="font-size: 14px; color: #10b981; font-weight: 600; margin: 4px 0 0;">{{ $invoice->invoice_number }}</p>
                <span class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
            </td>
        </tr>
    </table>

    <!-- Meta Info -->
    <table style="width:100%; margin-bottom: 25px;">
        <tr>
            <td style="width:50%; vertical-align: top;">
                <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Bill To</p>
                <p style="font-weight: 700; font-size: 14px;">{{ $invoice->customer->name }}</p>
                @if($invoice->customer->email)<p style="color: #64748b;">{{ $invoice->customer->email }}</p>@endif
                @if($invoice->customer->phone)<p style="color: #64748b;">{{ $invoice->customer->phone }}</p>@endif
                @if($invoice->customer->address)<p style="color: #64748b;">{{ $invoice->customer->address }}</p>@endif
            </td>
            <td style="width:50%; vertical-align: top; text-align: right;">
                <table style="float: right;">
                    <tr><td style="color: #94a3b8; font-size: 10px; padding: 3px 15px 3px 0;">Issue Date</td><td style="font-weight: 600;">{{ $invoice->issue_date->format('M d, Y') }}</td></tr>
                    <tr><td style="color: #94a3b8; font-size: 10px; padding: 3px 15px 3px 0;">Due Date</td><td style="font-weight: 600; {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'color: #dc2626;' : '' }}">{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th class="text-center" style="width:12%">Qty</th>
                <th class="text-right" style="width:19%">Unit Price</th>
                <th class="text-right" style="width:19%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                <td class="text-right" style="font-weight: 600;">₱{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr><td class="label">Subtotal</td><td class="value">₱{{ number_format($invoice->total_amount, 2) }}</td></tr>
            @if($invoice->penalty_amount > 0)
            <tr><td class="label" style="color: #dc2626;">Penalty</td><td class="value" style="color: #dc2626;">₱{{ number_format($invoice->penalty_amount, 2) }}</td></tr>
            @endif
            <tr><td class="label">Total Paid</td><td class="value" style="color: #10b981;">- ₱{{ number_format($invoice->total_paid, 2) }}</td></tr>
            <tr class="grand-total">
                <td>Balance Due</td>
                <td style="text-align: right;">₱{{ number_format($invoice->balance, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Payment History -->
    @if($invoice->payments->count())
    <div class="payment-history">
        <h3>Payment History</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                    <td>{{ $payment->reference_number ?? '—' }}</td>
                    <td class="text-right" style="color: #10b981; font-weight: 600;">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($invoice->notes)
    <div class="notes"><strong>Notes:</strong> {{ $invoice->notes }}</div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y h:i A') }} — TITA Finance System</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>

