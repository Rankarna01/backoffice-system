<?php

namespace App\Domain\Billing\Models;

use App\Domain\Billing\Enums\AffiliateStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'affiliates';

    protected $fillable = [
        'user_id',
        'code',
        'status',
        'commission_rate',
        'payout_details',
        'total_earnings',
        'total_paid',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AffiliateStatus::class,
            'commission_rate' => 'decimal:2',
            'payout_details' => 'array',
            'total_earnings' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function unpaidBalance(): float
    {
        return max(0, (float) $this->total_earnings - (float) $this->total_paid);
    }

    public function recalculateTotals(): void
    {
        $earnings = $this->commissions()->whereIn('status', ['approved', 'paid'])->sum('amount');
        $paid = $this->payouts()->where('status', 'paid')->sum('amount');

        $this->updateQuietly([
            'total_earnings' => $earnings,
            'total_paid' => $paid,
        ]);
    }
}
