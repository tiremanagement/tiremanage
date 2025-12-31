<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Receipt</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color:#333; }
        .container { max-width:700px; margin:0 auto; padding:24px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; }
        .brand { font-weight:700; color:#0b5; }
        .meta { text-align:right; font-size:13px; color:#666; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { padding:8px 6px; border-bottom:1px solid #eee; text-align:left; }
        .total { font-weight:700; font-size:18px; }
        .footer { margin-top:20px; font-size:13px; color:#666; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <div class="brand">Tire Management</div>
            <div style="font-size:13px;color:#666">Receipt</div>
        </div>
        <div class="meta">
            <div>Receipt #: {{ $receipt->id }}</div>
            <div>Date: {{ optional($receipt->created_at)->format('M d, Y') ?? now()->format('M d, Y') }}</div>
        </div>
    </div>

    <div>
        <strong>To:</strong>
        <div>{{ optional($receipt->supplier)->name ?? 'Supplier' }}</div>
        <div style="font-size:13px;color:#666">{{ optional($receipt->supplier)->contact ?? '' }} {{ optional($receipt->supplier)->email ? '· ' . optional($receipt->supplier)->email : '' }}</div>
    </div>

    <table>
        <tr>
            <th>Driver</th>
            <td>{{ optional($receipt->tireRequest->user)->name ?? optional($receipt->user)->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Vehicle</th>
            <td>{{ optional($receipt->tireRequest->vehicle)->plate_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Tire</th>
            <td>{{ optional($receipt->tireRequest->tire)->brand ?? '' }} {{ optional($receipt->tireRequest->tire)->size ?? '' }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ optional($receipt->tireRequest)->tire_count ?? '-' }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $receipt->description ?? '-' }}</td>
        </tr>
        <tr>
            <th class="total">Amount</th>
            <td class="total">₹ {{ number_format($receipt->amount, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <div>Issued by: {{ optional(auth()->user())->name ?? 'Transport Officer' }}</div>
        <div style="margin-top:8px;">If you have any questions about this receipt, please reply to this email.</div>
    </div>
</div>
</body>
</html>
