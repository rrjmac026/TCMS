<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Discount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'applicable_plans',
        'applicable_actions',
        'tenant_id',
        'valid_from',
        'valid_until',
        'max_uses',
        'uses_count',
        'minimum_price',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'applicable_plans'   => 'array',
        'applicable_actions' => 'array',
        'valid_from'         => 'date',
        'valid_until'        => 'date',
        'is_active'          => 'boolean',
        'value'              => 'decimal:2',
        'minimum_price'      => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('deleted_at');
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('uses_count', '<', 'max_uses');
            });
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Calculate the discounted price for a given base price.
     */
    public function applyTo(float $basePrice): float
    {
        if ($this->type === 'percentage') {
            $discount = $basePrice * ($this->value / 100);
        } else {
            $discount = (float) $this->value;
        }

        return max(0, round($basePrice - $discount, 2));
    }

    /**
     * Amount saved (not the final price).
     */
    public function discountAmount(float $basePrice): float
    {
        return round($basePrice - $this->applyTo($basePrice), 2);
    }

    /**
     * Check if this discount can be used for a given plan slug and action.
     */
    public function isValidFor(string $planSlug, string $action, ?string $tenantId = null, float $price = 0): bool
    {
        if (! $this->is_active || $this->trashed()) return false;

        // Date window
        if ($this->valid_from && now()->lt($this->valid_from->startOfDay())) return false;
        if ($this->valid_until && now()->gt($this->valid_until->endOfDay())) return false;

        // Usage cap
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) return false;

        // Tenant restriction
        if ($this->tenant_id && $this->tenant_id !== $tenantId) return false;

        // Plan restriction
        if ($this->applicable_plans && ! in_array($planSlug, $this->applicable_plans)) return false;

        // Action restriction
        if ($this->applicable_actions && ! in_array($action, $this->applicable_actions)) return false;

        // Minimum price gate
        if ($this->minimum_price !== null && $price < $this->minimum_price) return false;

        return true;
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active)                                      return 'Inactive';
        if ($this->valid_until && now()->gt($this->valid_until))     return 'Expired';
        if ($this->valid_from  && now()->lt($this->valid_from))      return 'Scheduled';
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) return 'Exhausted';
        return 'Active';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status_label) {
            'Active'    => 'success',
            'Scheduled' => 'warning',
            'Expired',
            'Exhausted',
            'Inactive'  => 'danger',
            default     => 'gray',
        };
    }

    public function getFormattedValueAttribute(): string
    {
        return $this->type === 'percentage'
            ? $this->value . '%'
            : '₱' . number_format($this->value, 2);
    }
}