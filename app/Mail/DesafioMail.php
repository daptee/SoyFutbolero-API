<?php

    namespace App\Mail;

    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Mail\Mailable;
    use Illuminate\Queue\SerializesModels;

    class DesafioMail extends Mailable
    {
        use Queueable, SerializesModels;

        protected $user_create;

        /**
         * Create a new message instance.
         *
         * @return void
         */
        public function __construct($user_create)
        {
            $this->user_create = $user_create;
        }

        /**
         * Build the message.
         *
         * @return $this
         */
        public function build()
        {
            return $this->view('Mails.desafio_invitation')
                ->subject('Te invitaron a un Desafio en SoyFutbolero.com')
                ->with([
                    'name' => $this->user_create->nombre,
                ]);
        }
    }
