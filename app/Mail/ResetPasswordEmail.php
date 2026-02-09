<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetCode;
    public $expiresAt;

    /**
     * Create a new message instance.
     *
     * @param string $resetCode
     * @param \Illuminate\Support\Carbon $expiresAt
     * @return void
     */
    public function __construct($resetCode, $expiresAt)
    {
        $this->resetCode = $resetCode;
        $this->expiresAt = $expiresAt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Ihr Passwort-Reset-Code')
                    ->view('emails.reset_password')
                    ->with([
                        'resetCode' => $this->resetCode,
                        'expiresAt' => $this->expiresAt,
                        'verifyUrl' => route('password.verify'),
                    ]);
    }
}