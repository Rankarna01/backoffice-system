<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Toolbar: Period Filter & Sync Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rentang Periode:</span>
                <div class="inline-flex rounded-lg p-1 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="$set('period', '7_days')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition {{ $period === '7_days' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                    >
                        7 Hari
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', '30_days')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition {{ $period === '30_days' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                    >
                        30 Hari
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', 'this_month')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition {{ $period === 'this_month' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                    >
                        Bulan Ini
                    </button>
                    <button
                        type="button"
                        wire:click="$set('period', 'all_time')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition {{ $period === 'all_time' ? 'bg-white dark:bg-gray-700 text-primary-600 dark:text-primary-400 shadow-sm font-semibold' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}"
                    >
                        Semua Waktu
                    </button>
                </div>
            </div>

            <x-filament::button
                type="button"
                wire:click="refreshSnapshots"
                icon="bx-refresh"
                color="gray"
            >
                Sinkronkan Snapshot Hari Ini
            </x-filament::button>
        </div>

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Gross Revenue -->
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Omzet Penjualan</p>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 rounded-lg">
                        <x-filament::icon icon="bx-money" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    Rp {{ number_format($this->metrics['gross_revenue'], 0, ',', '.') }}
                </p>
                <div class="flex items-center gap-1.5 mt-2 text-xs text-emerald-600 font-medium">
                    <x-filament::icon icon="bx-trending-up" class="w-4 h-4" />
                    <span>Est. Net: Rp {{ number_format($this->metrics['net_revenue'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Active Subscriptions -->
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Member Berlangganan Aktif</p>
                    <div class="p-2 bg-purple-50 dark:bg-purple-950/40 text-purple-600 rounded-lg">
                        <x-filament::icon icon="bx-id-card" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ number_format($this->metrics['active_subscribers']) }}
                </p>
                <div class="flex items-center gap-1.5 mt-2 text-xs text-purple-600 font-medium">
                    <x-filament::icon icon="bx-check-shield" class="w-4 h-4" />
                    <span>Status VIP & Pro Trader</span>
                </div>
            </div>

            <!-- New Students -->
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Pertumbuhan Murid Baru</p>
                    <div class="p-2 bg-blue-50 dark:bg-blue-950/40 text-blue-600 rounded-lg">
                        <x-filament::icon icon="bx-user-plus" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ number_format($this->metrics['new_students_count']) }}
                </p>
                <div class="flex items-center gap-1.5 mt-2 text-xs text-blue-600 font-medium">
                    <x-filament::icon icon="bx-check-circle" class="w-4 h-4" />
                    <span>{{ $this->metrics['paid_orders_count'] }} Transaksi Sukses</span>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Tingkat Konversi Order (Paid)</p>
                    <div class="p-2 bg-amber-50 dark:bg-amber-950/40 text-amber-600 rounded-lg">
                        <x-filament::icon icon="bx-pie-chart-alt-2" class="w-5 h-5" />
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ $this->metrics['conversion_rate'] }}%
                </p>
                <div class="flex items-center gap-1.5 mt-2 text-xs text-amber-600 font-medium">
                    <x-filament::icon icon="bx-line-chart" class="w-4 h-4" />
                    <span>Rasio Order Sukses / Total Invoices</span>
                </div>
            </div>
        </div>

        <!-- 2 Columns: Top Performing Courses & Payment Methods Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Top Selling Courses (2 cols) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="bx-trophy" class="w-5 h-5 text-amber-500" />
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">5 Kursus Paling Populer & Berpenghasilan Tertinggi</h3>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Berdasarkan data enrollment</span>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach($this->topCourses as $course)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                            <div class="space-y-1">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $course['title'] }}</h4>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 font-medium">{{ $course['level'] }}</span>
                                    <span><strong>{{ number_format($course['enrollments']) }}</strong> Murid Terdaftar</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($course['revenue'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-400">Akumulasi Penjualan</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Methods & Channels Distribution (1 col) -->
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5 space-y-5">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="bx-wallet" class="w-5 h-5 text-primary-500" />
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Distribusi Gateway Pembayaran</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-gray-700 dark:text-gray-300">Midtrans (QRIS & Virtual Account)</span>
                            <span class="text-gray-900 dark:text-white font-bold">60%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-gray-700 dark:text-gray-300">Transfer Bank Manual (BCA / Mandiri)</span>
                            <span class="text-gray-900 dark:text-white font-bold">30%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium mb-1">
                            <span class="text-gray-700 dark:text-gray-300">Xendit Invoice</span>
                            <span class="text-gray-900 dark:text-white font-bold">10%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-800 space-y-2">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ringkasan Aktivitas Belajar</p>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">92.4%</p>
                            <p class="text-[11px] text-gray-500">Tingkat Lolos Kuis</p>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">88%</p>
                            <p class="text-[11px] text-gray-500">Kehadiran Webinar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Historical Snapshots Table -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="bx-calendar-check" class="w-5 h-5 text-primary-500" />
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Snapshot Metrik Harian</h3>
                </div>
                <span class="text-xs text-gray-500">14 Hari Terakhir</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-xs uppercase font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Omzet Bruto</th>
                            <th class="px-5 py-3">Order Sukses / Total</th>
                            <th class="px-5 py-3">User Baru</th>
                            <th class="px-5 py-3">Subscribers Aktif</th>
                            <th class="px-5 py-3">Modul Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($this->dailySnapshots as $snapshot)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">
                                    {{ $snapshot->snapshot_date ? $snapshot->snapshot_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-5 py-3.5 font-semibold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($snapshot->gross_revenue, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ $snapshot->paid_orders_count }}</span>
                                    <span class="text-xs text-gray-400">/ {{ $snapshot->new_orders_count }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    +{{ $snapshot->new_users_count }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-200">
                                    {{ $snapshot->active_subscribers_count }}
                                </td>
                                <td class="px-5 py-3.5">
                                    {{ $snapshot->lesson_completions_count }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                    Belum ada data snapshot harian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>