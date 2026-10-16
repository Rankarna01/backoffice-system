<?php

namespace App\Domain\Trading\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperTradingPosition extends Model
{
    use HasFactory;

    protected $table = 'paper_trading_positions';

    protected $fillable = [
        'paper_trading_account_id',
        'user_id',
        'symbol',
        'side',
        'units',
        'entry_price',
        'current_price',
        'stop_loss',
        'take_profit',
        'profit_loss',
        'status',
        'opened_at',
        'closed_at',
        'close_price',
        'close_reason',
    ];

    protected function casts(): array
    {
        return [
            'units' => 'decimal:4',
            'entry_price' => 'decimal:4',
            'current_price' => 'decimal:4',
            'stop_loss' => 'decimal:4',
            'take_profit' => 'decimal:4',
            'profit_loss' => 'decimal:2',
            'close_price' => 'decimal:4',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaperTradingAccount::class, 'paper_trading_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Hitung P/L berdasarkan harga pasar terkini.
     */
    public function updatePriceAndPnl(float $marketPrice): void
    {
        $this->current_price = $marketPrice;

        if ($this->side === 'buy') {
            $diff = $marketPrice - (float) $this->entry_price;
        } else {
            $diff = (float) $this->entry_price - $marketPrice;
        }

        // Kalkulasi P/L sederhana (tanpa leverage, nominal lot/unit * price difference)
        $this->profit_loss = round($diff * (float) $this->units, 2);
        $this->save();

        $this->account?->recalculateEquity();
    }

    /**
     * Tutup posisi order dan kembalikan/tambahkan P/L ke saldo akun.
     */
    public function close(float $exitPrice, string $reason = 'manual'): void
    {
        $this->close_price = $exitPrice;
        $this->closed_at = now();
        $this->status = 'closed';
        $this->close_reason = $reason;

        if ($this->side === 'buy') {
            $diff = $exitPrice - (float) $this->entry_price;
        } else {
            $diff = (float) $this->entry_price - $exitPrice;
        }

        $finalPnl = round($diff * (float) $this->units, 2);
        $this->profit_loss = $finalPnl;
        $this->save();

        if ($this->account) {
            $this->account->balance = (float) $this->account->balance + $finalPnl;
            $this->account->recalculateEquity();
        }
    }
}
