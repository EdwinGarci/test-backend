<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VouchersFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $failedVouchers;
    public User $user;

    public function __construct(array $failedVouchers, User $user)
    {
        $this->failedVouchers = $failedVouchers;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.vouchers_failed')
            ->with(['failedVouchers' => $this->failedVouchers, 'user' => $this->user]);
    }
}
