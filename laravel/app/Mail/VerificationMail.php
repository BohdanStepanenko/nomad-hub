<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected User $user,
    ) {
    }

    public function build(): self
    {
        $url = env('FRONTEND_URL') . '/verify-email/' . $this->user->verification_token;

        return $this
            ->subject('Email Verification')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view(
                'emails.verification',
                [
                    'name' => $this->user->name,
                    'url' => $url,
                ]
            );
    }
}
