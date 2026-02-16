<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code; 

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        $this->code = $code; 
    } 

    /**
     * Build the message.
     */ 
    public function build()
    {
        return $this->subject('Verify Your Email')
            ->view('emails.email-verification');
    }
}
