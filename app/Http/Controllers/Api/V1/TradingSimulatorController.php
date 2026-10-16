<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Trading\Models\PaperTradingAccount;
use App\Domain\Trading\Models\PaperTradingPosition;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingSimulatorController extends Controller
{
    /**
     * Get or initialize current user's paper trading account with balance and equity.
     */
    public function getAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $account = PaperTradingAccount::getOrCreateForUser($user, 10000.00);
        $account->recalculateEquity();

        $openPositionsCount = $account->openPositions()->count();
        $unrealizedPnl = (float) $account->openPositions()->sum('profit_loss');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'balance' => (float) $account->balance,
                'equity' => (float) $account->equity,
                'unrealized_pnl' => $unrealizedPnl,
                'currency' => $account->currency,
                'open_positions_count' => $openPositionsCount,
                'status' => $account->status,
            ],
        ]);
    }

    /**
     * Get all open positions and trade history for the user.
     */
    public function getPositions(Request $request): JsonResponse
    {
        $user = $request->user();
        $account = PaperTradingAccount::getOrCreateForUser($user);

        $openPositions = $account->openPositions()->latest('opened_at')->get();
        $history = $account->closedPositions()->take(50)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'open_positions' => $openPositions->map(fn ($p) => [
                    'id' => $p->id,
                    'symbol' => $p->symbol,
                    'side' => $p->side,
                    'units' => (float) $p->units,
                    'entry_price' => (float) $p->entry_price,
                    'current_price' => (float) ($p->current_price ?? $p->entry_price),
                    'stop_loss' => $p->stop_loss ? (float) $p->stop_loss : null,
                    'take_profit' => $p->take_profit ? (float) $p->take_profit : null,
                    'profit_loss' => (float) $p->profit_loss,
                    'opened_at' => $p->opened_at->toIso8601String(),
                ]),
                'history' => $history->map(fn ($p) => [
                    'id' => $p->id,
                    'symbol' => $p->symbol,
                    'side' => $p->side,
                    'units' => (float) $p->units,
                    'entry_price' => (float) $p->entry_price,
                    'close_price' => (float) ($p->close_price ?? $p->entry_price),
                    'stop_loss' => $p->stop_loss ? (float) $p->stop_loss : null,
                    'take_profit' => $p->take_profit ? (float) $p->take_profit : null,
                    'profit_loss' => (float) $p->profit_loss,
                    'close_reason' => $p->close_reason,
                    'opened_at' => $p->opened_at->toIso8601String(),
                    'closed_at' => $p->closed_at ? $p->closed_at->toIso8601String() : null,
                ]),
            ],
        ]);
    }

    /**
     * Open a new live trading order (BUY or SELL).
     */
    public function openOrder(Request $request): JsonResponse
    {
        $request->validate([
            'symbol' => 'required|string',
            'side' => 'required|in:buy,sell',
            'units' => 'required|numeric|min:0.0001',
            'entry_price' => 'required|numeric|min:0.0001',
            'stop_loss' => 'nullable|numeric',
            'take_profit' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $account = PaperTradingAccount::getOrCreateForUser($user);

        $position = PaperTradingPosition::create([
            'paper_trading_account_id' => $account->id,
            'user_id' => $user->id,
            'symbol' => $request->symbol,
            'side' => $request->side,
            'units' => $request->units,
            'entry_price' => $request->entry_price,
            'current_price' => $request->entry_price,
            'stop_loss' => $request->stop_loss,
            'take_profit' => $request->take_profit,
            'profit_loss' => 0.00,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $account->recalculateEquity();

        return response()->json([
            'success' => true,
            'message' => 'Posisi trading berhasil dibuka.',
            'data' => [
                'position_id' => $position->id,
                'symbol' => $position->symbol,
                'side' => $position->side,
                'entry_price' => (float) $position->entry_price,
                'units' => (float) $position->units,
                'account_balance' => (float) $account->balance,
                'account_equity' => (float) $account->equity,
            ],
        ], 201);
    }

    /**
     * Close an active open position and credit/debit realized P/L to balance.
     */
    public function closePosition(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $account = PaperTradingAccount::getOrCreateForUser($user);

        $position = PaperTradingPosition::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (! $position) {
            return response()->json([
                'success' => false,
                'message' => 'Posisi tidak ditemukan atau sudah ditutup.',
            ], 404);
        }

        $exitPrice = (float) ($request->input('exit_price') ?? $position->current_price ?? $position->entry_price);
        $position->close($exitPrice, 'manual');

        $account->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Posisi berhasil ditutup.',
            'data' => [
                'position_id' => $position->id,
                'realized_pnl' => (float) $position->profit_loss,
                'exit_price' => $exitPrice,
                'new_balance' => (float) $account->balance,
                'new_equity' => (float) $account->equity,
            ],
        ]);
    }

    /**
     * Update Stop Loss (SL) and Take Profit (TP) levels.
     */
    public function updateSlTp(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'stop_loss' => 'nullable|numeric',
            'take_profit' => 'nullable|numeric',
        ]);

        $user = $request->user();
        $position = PaperTradingPosition::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (! $position) {
            return response()->json([
                'success' => false,
                'message' => 'Posisi tidak ditemukan.',
            ], 404);
        }

        $position->stop_loss = $request->input('stop_loss');
        $position->take_profit = $request->input('take_profit');
        $position->save();

        return response()->json([
            'success' => true,
            'message' => 'Tingkat SL/TP berhasil diperbarui.',
            'data' => [
                'position_id' => $position->id,
                'stop_loss' => $position->stop_loss ? (float) $position->stop_loss : null,
                'take_profit' => $position->take_profit ? (float) $position->take_profit : null,
            ],
        ]);
    }

    /**
     * Reset user's simulation balance back to $10,000.00.
     */
    public function resetAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $account = PaperTradingAccount::getOrCreateForUser($user);

        // Close all open positions
        foreach ($account->openPositions as $position) {
            $position->close((float) ($position->current_price ?? $position->entry_price), 'user_reset');
        }

        $account->balance = 10000.00;
        $account->equity = 10000.00;
        $account->status = 'active';
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Saldo simulasi berhasil di-reset kembali ke $10,000.00.',
            'data' => [
                'balance' => 10000.00,
                'equity' => 10000.00,
            ],
        ]);
    }
}
