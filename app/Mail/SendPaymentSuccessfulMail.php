<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPaymentSuccessfulMail extends Mailable
{
    use Queueable, SerializesModels;
    public $message;
    /**
     * Create a new message instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Course Payment Notification')->markdown('emails.payments')
            ->with('message', $this->message);
    }
}
