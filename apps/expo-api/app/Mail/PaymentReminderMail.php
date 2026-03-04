<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $amount;
    public string $dueDate;

    public function __construct(string $userName, string $amount, string $dueDate)
    {
        $this->userName = $userName;
        $this->amount = $amount;
        $this->dueDate = $dueDate;
    }

    public function build()
    {
        return $this->subject('تذكير بموعد الدفع - معام اكسبو')
            ->view('emails.payment-reminder', [
                'userName' => $this->userName,
                'amount' => $this->amount,
                'dueDate' => $this->dueDate,
                'greeting' => 'مرحباً',
            ]);
    }
}
