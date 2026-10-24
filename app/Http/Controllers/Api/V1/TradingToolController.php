<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TradingToolController extends Controller
{
    /**
     * Indicative market rates and instrument specifications.
     */
    protected array $instruments = [
        'EURUSD' => [
            'symbol' => 'EURUSD',
            'name' => 'Euro / US Dollar',
            'type' => 'forex',
            'base' => 'EUR',
            'quote' => 'USD',
            'price' => 1.0850,
            'pip_size' => 0.0001,
            'contract_size' => 100000,
        ],
        'GBPUSD' => [
            'symbol' => 'GBPUSD',
            'name' => 'British Pound / US Dollar',
            'type' => 'forex',
            'base' => 'GBP',
            'quote' => 'USD',
            'price' => 1.2980,
            'pip_size' => 0.0001,
            'contract_size' => 100000,
        ],
        'USDJPY' => [
            'symbol' => 'USDJPY',
            'name' => 'US Dollar / Japanese Yen',
            'type' => 'forex',
            'base' => 'USD',
            'quote' => 'JPY',
            'price' => 152.40,
            'pip_size' => 0.01,
            'contract_size' => 100000,
        ],
        'XAUUSD' => [
            'symbol' => 'XAUUSD',
            'name' => 'Gold (Spot) / US Dollar',
            'type' => 'commodity',
            'base' => 'XAU',
            'quote' => 'USD',
            'price' => 2735.50,
            'pip_size' => 0.01,
            'contract_size' => 100,
        ],
        'BTCUSD' => [
            'symbol' => 'BTCUSD',
            'name' => 'Bitcoin / US Dollar',
            'type' => 'crypto',
            'base' => 'BTC',
            'quote' => 'USD',
            'price' => 67500.00,
            'pip_size' => 1.0,
            'contract_size' => 1,
        ],
        'AUDUSD' => [
            'symbol' => 'AUDUSD',
            'name' => 'Australian Dollar / US Dollar',
            'type' => 'forex',
            'base' => 'AUD',
            'quote' => 'USD',
            'price' => 0.6650,
            'pip_size' => 0.0001,
            'contract_size' => 100000,
        ],
        'USDCHF' => [
            'symbol' => 'USDCHF',
            'name' => 'US Dollar / Swiss Franc',
            'type' => 'forex',
            'base' => 'USD',
            'quote' => 'CHF',
            'price' => 0.8650,
            'pip_size' => 0.0001,
            'contract_size' => 100000,
        ],
        'GBPJPY' => [
            'symbol' => 'GBPJPY',
            'name' => 'British Pound / Japanese Yen',
            'type' => 'forex',
            'base' => 'GBP',
            'quote' => 'JPY',
            'price' => 197.80,
            'pip_size' => 0.01,
            'contract_size' => 100000,
        ],
        'EURJPY' => [
            'symbol' => 'EURJPY',
            'name' => 'Euro / Japanese Yen',
            'type' => 'forex',
            'base' => 'EUR',
            'quote' => 'JPY',
            'price' => 165.30,
            'pip_size' => 0.01,
            'contract_size' => 100000,
        ],
        'USOIL' => [
            'symbol' => 'USOIL',
            'name' => 'Crude Oil WTI (Spot)',
            'type' => 'commodity',
            'base' => 'USOIL',
            'quote' => 'USD',
            'price' => 71.50,
            'pip_size' => 0.01,
            'contract_size' => 1000,
        ],
        'US30' => [
            'symbol' => 'US30',
            'name' => 'Dow Jones Industrial Average',
            'type' => 'index',
            'base' => 'US30',
            'quote' => 'USD',
            'price' => 42800.00,
            'pip_size' => 1.0,
            'contract_size' => 1,
        ],
    ];

    protected float $usdToIdr = 15500.0;

    /**
     * Get indicative instrument market rates & specifications.
     * GET /api/v1/trading/tools/rates
     */
    public function rates(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'usd_to_idr' => $this->usdToIdr,
            'data' => array_values($this->instruments),
            'disclaimer' => 'Harga bersifat indikatif untuk kalkulasi matematis, bukan kuotasi eksekusi broker live.',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Calculate Pip Value.
     * POST /api/v1/trading/tools/calculate-pip
     */
    public function calculatePip(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'lot_size' => 'required|numeric|min:0.01',
            'pips' => 'required|numeric|min:0.1',
            'account_currency' => 'nullable|string|in:USD,IDR',
            'custom_price' => 'nullable|numeric',
        ]);

        $pairKey = strtoupper(str_replace(['/', '-', '_', ' '], '', $validated['pair']));
        $instrument = $this->instruments[$pairKey] ?? [
            'symbol' => $pairKey,
            'name' => $pairKey,
            'type' => 'forex',
            'base' => substr($pairKey, 0, 3),
            'quote' => substr($pairKey, 3, 3) ?: 'USD',
            'price' => (float) ($validated['custom_price'] ?? 1.0),
            'pip_size' => str_ends_with($pairKey, 'JPY') ? 0.01 : 0.0001,
            'contract_size' => 100000,
        ];

        $lotSize = (float) $validated['lot_size'];
        $pips = (float) $validated['pips'];
        $currency = strtoupper($validated['account_currency'] ?? 'USD');
        $pipSize = $instrument['pip_size'];
        $contractSize = $instrument['contract_size'];
        $price = (float) ($validated['custom_price'] ?? $instrument['price']);

        // Pip value in quote currency
        $pipValueQuote = $pipSize * $contractSize * $lotSize;

        // Convert pip value to USD
        $pipValueUsd = $pipValueQuote;
        if ($instrument['quote'] === 'JPY') {
            $pipValueUsd = $price > 0 ? ($pipValueQuote / $price) : 0;
        } elseif ($instrument['quote'] === 'CHF') {
            $pipValueUsd = $price > 0 ? ($pipValueQuote / $price) : 0;
        } elseif ($instrument['quote'] !== 'USD' && $instrument['base'] === 'USD') {
            $pipValueUsd = $price > 0 ? ($pipValueQuote / $price) : 0;
        }

        // Convert to Account Currency
        $pipValue = $currency === 'IDR' ? ($pipValueUsd * $this->usdToIdr) : $pipValueUsd;
        $totalValue = $pipValue * $pips;

        return response()->json([
            'success' => true,
            'data' => [
                'pair' => $instrument['symbol'],
                'lot_size' => $lotSize,
                'pips' => $pips,
                'currency' => $currency,
                'pip_value' => round($pipValue, 2),
                'total_value' => round($totalValue, 2),
                'contract_size' => $contractSize,
                'pip_size' => $pipSize,
                'price_used' => $price,
            ],
        ]);
    }

    /**
     * Calculate Lot Size (Position Sizing).
     * POST /api/v1/trading/tools/calculate-lot
     */
    public function calculateLot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'account_balance' => 'required|numeric|min:1',
            'risk_percent' => 'nullable|numeric|min:0.1|max:100',
            'risk_amount' => 'nullable|numeric|min:1',
            'sl_pips' => 'required|numeric|min:1',
            'account_currency' => 'nullable|string|in:USD,IDR',
        ]);

        $pairKey = strtoupper(str_replace(['/', '-', '_', ' '], '', $validated['pair']));
        $instrument = $this->instruments[$pairKey] ?? [
            'symbol' => $pairKey,
            'pip_size' => str_ends_with($pairKey, 'JPY') ? 0.01 : 0.0001,
            'contract_size' => 100000,
            'quote' => substr($pairKey, 3, 3) ?: 'USD',
            'price' => 1.0,
        ];

        $balance = (float) $validated['account_balance'];
        $slPips = (float) $validated['sl_pips'];
        $currency = strtoupper($validated['account_currency'] ?? 'USD');

        // Determine risk amount
        if (isset($validated['risk_amount']) && $validated['risk_amount'] > 0) {
            $riskAmount = (float) $validated['risk_amount'];
            $riskPercent = ($riskAmount / $balance) * 100;
        } else {
            $riskPercent = (float) ($validated['risk_percent'] ?? 1.0);
            $riskAmount = ($balance * $riskPercent) / 100;
        }

        // Calculate pip value for 1.0 standard lot
        $pipSize = $instrument['pip_size'];
        $contractSize = $instrument['contract_size'];
        $pipValuePerLot = $pipSize * $contractSize;

        if ($instrument['quote'] === 'JPY' && $instrument['price'] > 0) {
            $pipValuePerLot = $pipValuePerLot / $instrument['price'];
        }

        if ($currency === 'IDR') {
            $pipValuePerLot = $pipValuePerLot * $this->usdToIdr;
        }

        // Recommended lot size: Risk Amount / (SL Pips * Pip Value per 1 Lot)
        $lotSize = $slPips > 0 && $pipValuePerLot > 0
            ? ($riskAmount / ($slPips * $pipValuePerLot))
            : 0;

        $recommendedLot = max(0.01, round($lotSize, 2));
        $units = (int) round($recommendedLot * $contractSize);

        // Risk grading
        $riskGrade = 'Konservatif (Aman)';
        if ($riskPercent > 5) {
            $riskGrade = 'Sangat Berisiko (Danger)';
        } elseif ($riskPercent > 3) {
            $riskGrade = 'Agresif (Tinggi)';
        } elseif ($riskPercent >= 1.5) {
            $riskGrade = 'Moderat (Standar)';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'pair' => $instrument['symbol'],
                'account_balance' => $balance,
                'risk_amount' => round($riskAmount, 2),
                'risk_percent' => round($riskPercent, 2),
                'sl_pips' => $slPips,
                'recommended_lot' => $recommendedLot,
                'units' => $units,
                'risk_grade' => $riskGrade,
                'currency' => $currency,
            ],
        ]);
    }

    /**
     * Calculate Risk & Reward Ratio and targets.
     * POST /api/v1/trading/tools/calculate-risk
     */
    public function calculateRisk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pair' => 'required|string',
            'position_type' => 'required|string|in:buy,sell,BUY,SELL',
            'entry_price' => 'required|numeric|gt:0',
            'sl_price' => 'required|numeric|gt:0',
            'tp_price' => 'required|numeric|gt:0',
            'lot_size' => 'nullable|numeric|min:0.01',
            'account_currency' => 'nullable|string|in:USD,IDR',
        ]);

        $pairKey = strtoupper(str_replace(['/', '-', '_', ' '], '', $validated['pair']));
        $instrument = $this->instruments[$pairKey] ?? [
            'symbol' => $pairKey,
            'pip_size' => str_ends_with($pairKey, 'JPY') ? 0.01 : 0.0001,
            'contract_size' => 100000,
            'quote' => 'USD',
            'price' => 1.0,
        ];

        $posType = strtolower($validated['position_type']);
        $entry = (float) $validated['entry_price'];
        $sl = (float) $validated['sl_price'];
        $tp = (float) $validated['tp_price'];
        $lotSize = (float) ($validated['lot_size'] ?? 1.0);
        $currency = strtoupper($validated['account_currency'] ?? 'USD');
        $pipSize = $instrument['pip_size'];

        // Distance in pips
        $slPips = abs($entry - $sl) / $pipSize;
        $tpPips = abs($tp - $entry) / $pipSize;

        // Ratio
        $rrRatio = $slPips > 0 ? round($tpPips / $slPips, 2) : 0;
        $breakevenWinRate = $rrRatio > 0 ? round((1 / (1 + $rrRatio)) * 100, 1) : 50.0;

        // Pip value per 1 lot
        $pipVal = $pipSize * $instrument['contract_size'] * $lotSize;
        if ($currency === 'IDR') {
            $pipVal = $pipVal * $this->usdToIdr;
        }

        $potentialLoss = round($slPips * $pipVal, 2);
        $potentialProfit = round($tpPips * $pipVal, 2);

        // Setup quality rating
        $grade = 'Kurang Ideal (RR Rendah)';
        if ($rrRatio >= 3.0) {
            $grade = 'Sangat Bagus (High Reward)';
        } elseif ($rrRatio >= 2.0) {
            $grade = 'Ideal (Rekomendasi)';
        } elseif ($rrRatio >= 1.5) {
            $grade = 'Dapat Diterima';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'position_type' => strtoupper($posType),
                'entry_price' => $entry,
                'sl_price' => $sl,
                'tp_price' => $tp,
                'sl_pips' => round($slPips, 1),
                'tp_pips' => round($tpPips, 1),
                'rr_ratio' => $rrRatio,
                'potential_loss' => $potentialLoss,
                'potential_profit' => $potentialProfit,
                'breakeven_winrate' => $breakevenWinRate,
                'setup_grade' => $grade,
                'currency' => $currency,
            ],
        ]);
    }

    /**
     * Calculate Compounding Growth.
     * POST /api/v1/trading/tools/calculate-compounding
     */
    public function calculateCompounding(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'principal' => 'required|numeric|min:1',
            'rate_percent' => 'required|numeric|min:0.1',
            'periods' => 'required|integer|min:1|max:120',
            'period_type' => 'nullable|string|in:daily,weekly,monthly,yearly',
            'deposit_per_period' => 'nullable|numeric|min:0',
            'reinvest_percent' => 'nullable|numeric|min:1|max:100',
            'currency' => 'nullable|string|in:USD,IDR',
        ]);

        $principal = (float) $validated['principal'];
        $rate = (float) $validated['rate_percent'] / 100.0;
        $periods = (int) $validated['periods'];
        $periodType = $validated['period_type'] ?? 'monthly';
        $deposit = (float) ($validated['deposit_per_period'] ?? 0);
        $reinvestRate = (float) ($validated['reinvest_percent'] ?? 100) / 100.0;
        $currency = strtoupper($validated['currency'] ?? 'USD');

        $currentBalance = $principal;
        $totalProfit = 0;
        $totalDeposits = 0;
        $breakdown = [];

        for ($i = 1; $i <= $periods; $i++) {
            $startBalance = $currentBalance;
            $periodProfit = $startBalance * $rate;
            $reinvestedProfit = $periodProfit * $reinvestRate;
            $withdrawnProfit = $periodProfit - $reinvestedProfit;

            $totalProfit += $periodProfit;
            $totalDeposits += $deposit;

            $endBalance = $startBalance + $reinvestedProfit + $deposit;
            $currentBalance = $endBalance;

            $breakdown[] = [
                'period' => $i,
                'start_balance' => round($startBalance, 2),
                'profit' => round($periodProfit, 2),
                'withdrawn' => round($withdrawnProfit, 2),
                'deposit' => round($deposit, 2),
                'end_balance' => round($endBalance, 2),
            ];
        }

        $finalBalance = round($currentBalance, 2);
        $netProfit = round($totalProfit, 2);
        $growthPercent = $principal > 0 ? round(($netProfit / $principal) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'principal' => $principal,
                'rate_percent' => (float) $validated['rate_percent'],
                'periods' => $periods,
                'period_type' => $periodType,
                'final_balance' => $finalBalance,
                'net_profit' => $netProfit,
                'total_deposits' => round($totalDeposits, 2),
                'growth_percent' => $growthPercent,
                'currency' => $currency,
                'breakdown' => $breakdown,
            ],
        ]);
    }
}
