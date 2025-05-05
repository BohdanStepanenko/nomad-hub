<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected User $user,
        protected string $resetLink
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Password Reset')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view(
                'emails.password-reset',
                [
                    'name' => $this->user->name,
                    'resetLink' => $this->resetLink,
                ]
            );
    }
}
