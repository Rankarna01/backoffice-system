<div class="space-y-6 text-xs">
    <!-- Account Summary Header -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-gray-900 border border-gray-800 rounded-xl">
        <div>
            <span class="text-gray-500">Nomor Akun</span>
            <p class="font-bold text-gray-200 text-sm">{{ $record->account_number }}</p>
        </div>
        <div>
            <span class="text-gray-500">Saldo Saat Ini</span>
            <p class="font-bold text-emerald-400 text-sm">${{ number_format($record->balance, 2) }}</p>
        </div>
        <div>
            <span class="text-gray-500">Ekuitas</span>
            <p class="font-bold text-indigo-400 text-sm">${{ number_format($record->equity, 2) }}</p>
        </div>
        <div>
            <span class="text-gray-500">Status</span>
            <p class="font-bold uppercase tracking-wider text-xs {{ $record->status === 'active' ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $record->status }}
            </p>
        </div>
    </div>

    <!-- Section 1: Posisi Terbuka (Open Positions) -->
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <h4 class="font-semibold text-gray-200 text-sm flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Posisi Terbuka Aktif ({{ $record->openPositions->count() }})
            </h4>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-400 font-medium">
                    <tr>
                        <th class="p-2.5">Simbol</th>
                        <th class="p-2.5">Arah</th>
                        <th class="p-2.5">Unit</th>
                        <th class="p-2.5">Entry</th>
                        <th class="p-2.5">Current</th>
                        <th class="p-2.5">SL</th>
                        <th class="p-2.5">TP</th>
                        <th class="p-2.5 text-right">P/L (USD)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 bg-gray-950">
                    @forelse($record->openPositions as $pos)
                        <tr class="hover:bg-gray-900/50">
                            <td class="p-2.5 font-bold text-gray-200">{{ $pos->symbol }}</td>
                            <td class="p-2.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $pos->side === 'buy' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ $pos->side }}
                                </span>
                            </td>
                            <td class="p-2.5 text-gray-300">{{ number_format($pos->units, 4) }}</td>
                            <td class="p-2.5 text-gray-300">${{ number_format($pos->entry_price, 2) }}</td>
                            <td class="p-2.5 text-gray-300">${{ number_format($pos->current_price ?? $pos->entry_price, 2) }}</td>
                            <td class="p-2.5 text-gray-400">{{ $pos->stop_loss ? '$' . number_format($pos->stop_loss, 2) : '-' }}</td>
                            <td class="p-2.5 text-gray-400">{{ $pos->take_profit ? '$' . number_format($pos->take_profit, 2) : '-' }}</td>
                            <td class="p-2.5 text-right font-bold {{ $pos->profit_loss >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $pos->profit_loss >= 0 ? '+' : '' }}${{ number_format($pos->profit_loss, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">
                                Tidak ada posisi terbuka yang sedang aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Riwayat Transaksi Selesai (Closed Positions) -->
    <div class="space-y-2">
        <h4 class="font-semibold text-gray-200 text-sm">Riwayat Transaksi Ditutup ({{ $record->closedPositions->count() }})</h4>

        <div class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-400 font-medium">
                    <tr>
                        <th class="p-2.5">Simbol</th>
                        <th class="p-2.5">Arah</th>
                        <th class="p-2.5">Unit</th>
                        <th class="p-2.5">Entry</th>
                        <th class="p-2.5">Exit</th>
                        <th class="p-2.5">Alasan Tutup</th>
                        <th class="p-2.5">Waktu</th>
                        <th class="p-2.5 text-right">Realized P/L</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800 bg-gray-950">
                    @forelse($record->closedPositions->take(10) as $pos)
                        <tr class="hover:bg-gray-900/50">
                            <td class="p-2.5 font-bold text-gray-200">{{ $pos->symbol }}</td>
                            <td class="p-2.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $pos->side === 'buy' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ $pos->side }}
                                </span>
                            </td>
                            <td class="p-2.5 text-gray-300">{{ number_format($pos->units, 4) }}</td>
                            <td class="p-2.5 text-gray-300">${{ number_format($pos->entry_price, 2) }}</td>
                            <td class="p-2.5 text-gray-300">${{ number_format($pos->close_price ?? $pos->entry_price, 2) }}</td>
                            <td class="p-2.5">
                                <span class="px-1.5 py-0.5 rounded text-[10px] bg-gray-800 text-gray-400 uppercase">
                                    {{ $pos->close_reason ?? 'manual' }}
                                </span>
                            </td>
                            <td class="p-2.5 text-gray-400">{{ $pos->closed_at ? $pos->closed_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="p-2.5 text-right font-bold {{ $pos->profit_loss >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $pos->profit_loss >= 0 ? '+' : '' }}${{ number_format($pos->profit_loss, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-gray-500">
                                Belum ada riwayat transaksi yang ditutup.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
