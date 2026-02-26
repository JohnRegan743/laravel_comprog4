<?php

namespace App\Mail;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public Transaction $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction->loadMissing(['user', 'items.product']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $pdf = Pdf::loadView('pdf.transaction_receipt', [
            'transaction' => $this->transaction,
        ]);

        return $this->subject('Your OfficeOne Transaction Receipt')
            ->view('emails.transaction_receipt')
            ->attachData(
                $pdf->output(),
                'receipt_' . $this->transaction->transaction_number . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}

