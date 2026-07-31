<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background: {{ $type === 'overdue' ? '#dc2626' : '#10b981' }}; color: white; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 4px 0 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 32px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .detail-label { color: #64748b; }
        .detail-value { font-weight: 600; color: #1e293b; }
        .amount { font-size: 28px; font-weight: 700; color: {{ $type === 'overdue' ? '#dc2626' : '#0ea5e9' }}; text-align: center; margin: 20px 0; }
        .footer { background: #f1f5f9; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $type === 'overdue' ? '⚠️ Overdue Notice' : '📋 Payment Reminder' }}</h1>
            <p>Invoice {{ $invoice->invoice_number }}</p>
        </div>
        <div class="body">
            <p>Dear <strong>{{ $invoice->customer->name }}</strong>,</p>

            @if($type === 'overdue')
            <p>This is to inform you that the following invoice is <strong>past due</strong>. Please settle your balance at your earliest convenience.</p>
            @else
            <p>This is a friendly reminder that the following invoice is due in <strong>3 days</strong>.</p>
            @endif

            <div class="amount">₱{{ number_format($invoice->balance, 2) }}</div>
            <p style="text-align: center; color: #64748b; font-size: 13px; margin-top: -10px;">Outstanding Balance</p>

            <div class="detail-row">
                <span class="detail-label">Invoice Number</span>
                <span class="detail-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Issue Date</span>
                <span class="detail-value">{{ $invoice->issue_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Due Date</span>
                <span class="detail-value" style="color: {{ $type === 'overdue' ? '#dc2626' : '#1e293b' }}">{{ $invoice->due_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span class="detail-value">₱{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="detail-value" style="color: #10b981;">₱{{ number_format($invoice->total_paid, 2) }}</span>
            </div>

            <p style="margin-top: 24px; font-size: 14px;">We accept payments via <strong>Cash</strong>, <strong>Bank Transfer</strong>, or <strong>GCash</strong>.</p>
            <p style="font-size: 14px;">If you have already made payment, please disregard this notice.</p>
        </div>
        <div class="footer">
            <p>TITA Finance System</p>
        </div>
    </div>
</body>
</html>

