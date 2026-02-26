@php
    /** @var \App\Models\Transaction $transaction */
@endphp

<p>Dear {{ $transaction->user->name }},</p>

<p>Thank you for your purchase from <strong>{{ config('app.name', 'OfficeOne Store') }}</strong>.</p>

<p>
    Your transaction <strong>{{ $transaction->transaction_number }}</strong> has a current status of
    <strong>{{ ucfirst($transaction->status) }}</strong> with a total amount of
    <strong>${{ number_format($transaction->total_amount, 2) }}</strong>.
</p>

<p>
    A detailed receipt in PDF format is attached to this email. It includes your order items and totals
    for your records.
</p>

@if($transaction->notes)
    <p><strong>Notes from the store:</strong> {{ $transaction->notes }}</p>
@endif

<p>If you have any questions, feel free to reply to this email.</p>

<p>Best regards,<br>
{{ config('app.name', 'OfficeOne Store') }}</p>

