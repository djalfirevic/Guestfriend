<?php

namespace App\Mails;

use Illuminate\Mail\Mailable;

/**
 * Class UserMail
 *
 * @package App\Mails
 */
class UserMail extends Mailable
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $message;

    /**
     * UserMail constructor.
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     */
    public function __construct(string $email, string $subject, string $message)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->email)
            ->subject($this->subject)
            ->html($this->message);
    }
}
