<?php

namespace App\Domain\Trading\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaperTradingAccount extends Model
{
    use HasFactory;

    protected $table = 'paper_trading_accounts';

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'equity',
        'currency',
        'initial_deposit',
        'status',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'equity' => 'decimal:2',
            'initial_deposit' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($account) {
            if (empty($account->account_number)) {
                $account->account_number = 'SIM-' . strtoupper(Str::random(6));
            }
            if (! isset($account->equity) || $account->equity === null) {
                $account->equity = $account->balance;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PaperTradingPosition::class, 'paper_trading_account_id');
    }

    public function openPositions(): HasMany
    {
        return $this->positions()->where('status', 'open');
    }

    public function closedPositions(): HasMany
    {
        return $this->positions()->where('status', 'closed')->latest('closed_at');
    }

    /**
     * Hitung P/L terbuka dan perbarui nilai equity.
     */
    public function recalculateEquity(): void
    {
        $unrealizedPnl = (float) $this->openPositions()->sum('profit_loss');
        $this->equity = (float) $this->balance + $unrealizedPnl;
        $this->save();
    }

    /**
     * Ambil atau buat akun trading demo default untuk seorang user.
     */
    public static function getOrCreateForUser(User $user, float $initialDeposit = 10000.00): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'account_number' => 'SIM-' . strtoupper(Str::random(6)),
                'balance' => $initialDeposit,
                'equity' => $initialDeposit,
                'currency' => 'USD',
                'initial_deposit' => $initialDeposit,
                'status' => 'active',
            ]
        );
    }
}
