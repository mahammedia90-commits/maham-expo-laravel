<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $userName;

    public function __construct(string $code, string $userName = '')
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('تأكيد البريد الإلكتروني - معام اكسبو')
            ->view('emails.email-verification', [
                'code' => $this->code,
                'userName' => $this->userName,
                'greeting' => 'مرحباً',
            ]);
    }
}
