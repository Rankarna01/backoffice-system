<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Trading\Models\JournalTrade;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TradingJournalController extends Controller
{
    /**
     * Resolve the current authenticated user or fallback user for preview.
     */
    protected function resolveUser(Request $request): User
    {
        return $request->user() 
            ?? User::where('email', 'budi@tradingedu.com')->first() 
            ?? User::firstOrFail();
    }

    /**
     * Display a paginated listing of the user's trading journal.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $query = JournalTrade::query()
            ->forUser($user->id)
            ->orderByDesc('entry_time');

        // Filter: Search (pair, setup, notes)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pair', 'like', "%{$search}%")
                    ->orWhere('setup', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter: Status (open, win, loss, breakeven, closed)
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter: Type (long, short)
        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->where('type', $type);
            }
        }

        $perPage = (int) $request->input('per_page', 15);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Get aggregate statistics for the user's journal.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $trades = JournalTrade::query()
            ->forUser($user->id)
            ->get();

        $totalTrades = $trades->count();
        $winTrades = $trades->where('status', 'win')->count();
        $lossTrades = $trades->where('status', 'loss')->count();
        $openTrades = $trades->where('status', 'open')->count();
        $breakevenTrades = $trades->where('status', 'breakeven')->count();

        $completedTrades = $winTrades + $lossTrades;
        $winRate = $completedTrades > 0 ? round(($winTrades / $completedTrades) * 100, 1) : 0;

        $totalPnL = (float) $trades->sum('profit_loss');
        $totalRisk = (float) $trades->sum('risk_amount');

        $disciplinedCount = $trades->where('is_disciplined', true)->count();
        $disciplineRate = $totalTrades > 0 ? round(($disciplinedCount / $totalTrades) * 100, 1) : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'total_trades' => $totalTrades,
                'win_trades' => $winTrades,
                'loss_trades' => $lossTrades,
                'open_trades' => $openTrades,
                'breakeven_trades' => $breakevenTrades,
                'win_rate' => $winRate,
                'total_pnl' => $totalPnL,
                'total_risk' => $totalRisk,
                'discipline_rate' => $disciplineRate,
            ],
        ]);
    }

    /**
     * Store a newly created trading journal record.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $validated = $request->validate([
            'entry_time' => 'required',
            'pair' => 'required|string|max:50',
            'type' => 'required|in:long,short',
            'status' => 'required|in:open,win,loss,breakeven,closed',
            'currency' => 'required|string|max:10',
            'risk_amount' => 'nullable|numeric|min:0',
            'profit_loss' => 'nullable|numeric',
            'setup' => 'nullable|string|max:100',
            'emotion' => 'nullable|string|max:100',
            'session' => 'nullable|string|max:100',
            'is_disciplined' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Format entry_time
        $validated['entry_time'] = Carbon::parse($validated['entry_time']);
        $validated['user_id'] = $user->id;
        $validated['risk_amount'] = $validated['risk_amount'] ?? 0;
        $validated['profit_loss'] = $validated['profit_loss'] ?? 0;
        $validated['is_disciplined'] = $request->boolean('is_disciplined', true);

        $trade = JournalTrade::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catatan trading berhasil disimpan ke jurnal.',
            'data' => $trade,
        ], 201);
    }

    /**
     * Display the specified trade journal item.
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        $trade = JournalTrade::query()
            ->forUser($user->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('uuid', $id);
            })
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $trade,
        ]);
    }

    /**
     * Update the specified trade journal item.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        $trade = JournalTrade::query()
            ->forUser($user->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('uuid', $id);
            })
            ->firstOrFail();

        $validated = $request->validate([
            'entry_time' => 'sometimes|required',
            'pair' => 'sometimes|required|string|max:50',
            'type' => 'sometimes|required|in:long,short',
            'status' => 'sometimes|required|in:open,win,loss,breakeven,closed',
            'currency' => 'sometimes|required|string|max:10',
            'risk_amount' => 'nullable|numeric|min:0',
            'profit_loss' => 'nullable|numeric',
            'setup' => 'nullable|string|max:100',
            'emotion' => 'nullable|string|max:100',
            'session' => 'nullable|string|max:100',
            'is_disciplined' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['entry_time'])) {
            $validated['entry_time'] = Carbon::parse($validated['entry_time']);
        }

        $trade->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catatan trading berhasil diperbarui.',
            'data' => $trade,
        ]);
    }

    /**
     * Remove the specified trade journal item from storage.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        $trade = JournalTrade::query()
            ->forUser($user->id)
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('uuid', $id);
            })
            ->firstOrFail();

        $trade->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan trade berhasil dihapus.',
        ]);
    }
}
