<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
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
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get all users that belong to this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if the tenant is on a specific plan.
     */
    public function hasSubscription(string $type): bool
    {
        return $this->subscription === $type;
    }



    /**
     * Check if the subscription is active and not expired.
     */
    public function isSubscribed(): bool
    {
        return $this->is_active && (! $this->expires_at || $this->expires_at->isFuture());
    }

    /**
     * Accessor for logo URL (if stored publicly).
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

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

    public function setCustomAttribute($key, $value)
    {
        if (in_array($key, self::getCustomColumns())) {
            $this->attributes[$key] = $value;
        } else {
            $data = $this->data ?? [];
            $data[$key] = $value;
            $this->data = $data;
        }
    }

    public function getCustomAttribute($key)
    {
        if (!is_string($key) && !is_int($key)) {
            return null;
        }

        $data = $this->data;

        if (!is_array($data)) {
            return null;
        }

        return $data[$key] ?? null;
    }




}
