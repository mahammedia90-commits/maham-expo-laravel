<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class PasswordResetMail extends Mailable
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
        return $this->subject('إعادة تعيين كلمة المرور - معام اكسبو')
            ->view('emails.password-reset', [
                'code' => $this->code,
                'userName' => $this->userName,
                'greeting' => 'مرحباً',
            ]);
    }
}
