<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SOA - {{ $customer->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 30px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table th { background: #f1f5f9; color: #475569; font-size: 10px; text-transform: uppercase;
                          letter-spacing: 1px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-partial { background: #fef3c7; color: #d97706; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .status-unpaid { background: #e2e8f0; color: #64748b; }

        .summary-box { border: 2px solid #10b981; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <table style="width:100%; margin-bottom: 20px; border-bottom: 3px solid #10b981; padding-bottom: 12px;">
        <tr>
            <td style="width:50%;">
                <h1 style="font-size: 24px; color: #10b981;">TITA</h1>
                <p style="font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: 2px;">Finance System</p>
            </td>
            <td style="text-align: right;">
                <h2 style="font-size: 18px; color: #334155; text-transform: uppercase;">Statement of Account</h2>
                <p style="font-size: 11px; color: #64748b;">Generated: {{ now()->format('M d, Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Customer Info -->
    <table style="width:100%; margin-bottom: 20px;">
        <tr>
            <td style="width:60%; vertical-align: top;">
                <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Customer</p>
                <p style="font-weight: 700; font-size: 14px;">{{ $customer->name }}</p>
                @if($customer->email)<p style="color: #64748b;">{{ $customer->email }}</p>@endif
                @if($customer->phone)<p style="color: #64748b;">{{ $customer->phone }}</p>@endif
                @if($customer->address)<p style="color: #64748b;">{{ $customer->address }}</p>@endif
            </td>
            <td style="width:40%; vertical-align: top;">
                <div class="summary-box">
                    <table style="width:100%;">
                        <tr><td style="color:#64748b; padding:3px 0; font-size: 11px;">Total Billed</td><td class="text-right" style="font-weight:600; padding:3px 0;">₱{{ number_format($customer->total_billed, 2) }}</td></tr>
                        <tr><td style="color:#10b981; padding:3px 0; font-size: 11px;">Total Paid</td><td class="text-right" style="font-weight:600; color:#10b981; padding:3px 0;">₱{{ number_format($customer->total_paid, 2) }}</td></tr>
                        <tr><td colspan="2" style="border-top:1px solid #e2e8f0; padding-top:8px;"></td></tr>
                        <tr><td style="font-weight:700; font-size:13px; padding:3px 0;">Balance Due</td><td class="text-right" style="font-weight:700; font-size:16px; color:{{ $customer->balance > 0 ? '#d97706' : '#10b981' }}; padding:3px 0;">₱{{ number_format($customer->balance, 2) }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Invoice List -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Issue Date</th>
                <th>Due Date</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Balance</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td style="font-weight: 600;">{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                <td class="text-right">₱{{ number_format($invoice->total_amount, 2) }}</td>
                <td class="text-right" style="color:#10b981;">₱{{ number_format($invoice->total_paid, 2) }}</td>
                <td class="text-right" style="font-weight:600; color:{{ $invoice->balance > 0 ? '#d97706' : '#10b981' }};">₱{{ number_format($invoice->balance, 2) }}</td>
                <td class="text-center"><span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top: 2px solid #334155;">
                <td colspan="3" style="font-weight:700; padding-top:10px;">TOTALS</td>
                <td class="text-right" style="font-weight:700; padding-top:10px;">₱{{ number_format($customer->total_billed, 2) }}</td>
                <td class="text-right" style="font-weight:700; color:#10b981; padding-top:10px;">₱{{ number_format($customer->total_paid, 2) }}</td>
                <td class="text-right" style="font-weight:700; color:{{ $customer->balance > 0 ? '#d97706' : '#10b981' }}; padding-top:10px;">₱{{ number_format($customer->balance, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p style="margin-top: 4px;">TITA Finance System — Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>

