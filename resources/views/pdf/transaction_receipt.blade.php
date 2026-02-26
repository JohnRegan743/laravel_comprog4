@php
    /** @var \App\Models\Transaction $transaction */
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
        }
        .header p {
            margin: 0;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-row th,
        .totals-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'OfficeOne Store') }}</h1>
        <p>Official Transaction Receipt</p>
    </div>

    <div>
        <div class="section-title">Customer Information</div>
        <table>
            <tr>
                <th style="width: 25%;">Name</th>
                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $transaction->user->email ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div>
        <div class="section-title">Transaction Details</div>
        <table>
            <tr>
                <th style="width: 25%;">Transaction #</th>
                <td>{{ $transaction->transaction_number }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ ucfirst($transaction->status) }}</td>
            </tr>
            <tr>
                <th>Date</th>
                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
            </tr>
            <tr>
                <th>Total Amount</th>
                <td>${{ number_format($transaction->total_amount, 2) }}</td>
            </tr>
            @if($transaction->notes)
                <tr>
                    <th>Notes</th>
                    <td>{{ $transaction->notes }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div>
        <div class="section-title">Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 45%;">Product</th>
                    <th style="width: 15%;" class="text-right">Unit Price</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaction->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="totals-row">
                    <td colspan="3" class="text-right">Grand Total</td>
                    <td class="text-right">${{ number_format($transaction->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="text-center" style="margin-top: 20px;">
        Thank you for your business.
    </p>
</body>
</html>

