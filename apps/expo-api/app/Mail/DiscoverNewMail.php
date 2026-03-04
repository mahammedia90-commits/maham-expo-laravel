<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class DiscoverNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $ctaUrl;

    public function __construct(string $userName, string $ctaUrl = '')
    {
        $this->userName = $userName;
        $this->ctaUrl = $ctaUrl ?: config('app.url', '#');
    }

    public function build()
    {
        return $this->subject('اكتشف الجديد على منصة معام اكسبو')
            ->view('emails.discover-new', [
                'userName' => $this->userName,
                'ctaUrl' => $this->ctaUrl,
                'greeting' => 'مرحباً',
            ]);
    }
}
