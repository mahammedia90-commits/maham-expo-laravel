<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct($link)
    {
        $this->link = $link; 
    }

    /**
     * Build the message.
     */ 
    public function build()
    {
        return $this->subject('Reset Your Password')
            ->view('emails.password-reset');
    }
}
