<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $receipt;

    /**
     * Create a new message instance.
     */
    public function __construct($receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Tire Receipt #' . $this->receipt->id)
                    ->view('emails.receipt')
                    ->with(['receipt' => $this->receipt]);
    }
}
