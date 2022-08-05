<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasword extends Mailable
{
    use Queueable, SerializesModels;

    protected $user_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->user_data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('Mails.reset_password')
        ->subject('Se restablecio su clave.')
        ->with([
            'password' => $this->user_data['password'],
        ]);
    }
}
