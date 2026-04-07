<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenewalRequest extends Model
{
    protected $connection = 'mysql'; // central DB

    protected $fillable = [
        'tenant_id', 'plan_slug', 'discount_code',
        'original_price', 'discount_amount', 'final_price',
        'status', 'reviewed_by', 'reviewed_at', 'notes',
    ];

    protected $casts = [
        'original_price'  => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price'     => 'decimal:2',
        'reviewed_at'     => 'datetime',
    ];

    public function tenant()   { return $this->belongsTo(Tenant::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isApproved() { return $this->status === 'approved'; }
}