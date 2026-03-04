<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    public function __construct(string $userName = '')
    {
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('مرحبًا بك في منصة معام اكسبو')
            ->view('emails.welcome', [
                'userName' => $this->userName,
                'greeting' => 'مرحباً',
            ]);
    }
}
