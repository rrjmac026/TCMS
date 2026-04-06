@component('mail::message')
# Subscription Expiring Soon

Hello, **{{ $tenant->name }}**!

Your **{{ ucfirst($tenant->subscription) }} Plan** subscription is expiring in **{{ $daysLeft }} day(s)**.

To continue using the system without interruption, please renew your subscription before it expires.

@component('mail::button', ['url' => 'https://' . $tenant->subdomain . '.tcm.com/admin/subscription', 'color' => 'primary'])
Renew Subscription
@endcomponent

**Subscription Details:**

| | |
|---|---|
| Plan | {{ ucfirst($tenant->subscription) }} |
| Expires On | {{ $tenant->expires_at->format('F d, Y') }} |
| Days Remaining | {{ $daysLeft }} day(s) |

If you have already renewed or have any concerns, please contact our support team.

Thanks,<br>
**TCMS — Technical Education and Skills Development Authority**

---
<small>This is an automated notification. Please do not reply to this email.</small>
@endcomponent
