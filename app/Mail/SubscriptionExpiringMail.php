<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public int    $daysLeft,
    ) {}

    public function build(): static
    {
        return $this->markdown('emails.tenant.subscription-expiring')
                    ->subject("Action Required: Your subscription expires in {$this->daysLeft} day(s)");
    }
}