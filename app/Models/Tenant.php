<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });
    }

    protected $fillable = [
        'id',
        'name',
        'admin_email',
        'subdomain',
        'subscription',
        'status',
        'expires_at',
        'data',
    ];

    protected $casts = [
        'data'       => 'array',
        'expires_at' => 'datetime',
    ];

    // Required by Stancl Tenancy — tells it which are real DB columns
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'admin_email',
            'subdomain',
            'subscription',
            'status',
            'expires_at',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasSubscription(string $type): bool
    {
        return $this->subscription === $type;
    }

    public function isSubscribed(): bool
    {
        return $this->status === 'approved' &&
               (! $this->expires_at || $this->expires_at->isFuture());
    }
}