<?php

namespace App\Domain\Billing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'commissions';

    protected $fillable = [
        'affiliate_id',
        'order_id',
        'amount',
        'rate',
        'status',
        'available_at',
        'payout_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rate' => 'decimal:2',
            'available_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function ($commission) {
            $commission->affiliate?->recalculateTotals();
        });
        static::deleted(function ($commission) {
            $commission->affiliate?->recalculateTotals();
        });
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
