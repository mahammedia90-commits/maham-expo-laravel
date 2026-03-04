<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class AccountConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    public function __construct(string $userName = '')
    {
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('تم تأكيد حسابك بنجاح - معام اكسبو')
            ->view('emails.account-confirmed', [
                'userName' => $this->userName,
                'greeting' => 'مرحباً',
            ]);
    }
}
