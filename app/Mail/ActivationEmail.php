<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $activationCode; 

    /**
     * Create a new message instance.
     *
     * @param  string  $activationCode
     * @return void
     */
    public function __construct($activationCode)
    {
        $this->activationCode = $activationCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'teamprojekt.knvk@gmail.com'), env('MAIL_FROM_NAME', 'TeamProjekt'))
                    ->subject('Ihr Aktivierungscode')
                    ->view('emails.activation') 
                    ->with(['code' => $this->activationCode]);
    }
}