<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class ConfirmMail extends Mailable
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

        return $this->view('Mails.confirm_mail')
        ->subject('SoyFutbolero - Confirmacion de cuenta')
        ->with([
            'id_encrypted' => Crypt::encryptString($this->user_data->id),
        ]);
    }
}
