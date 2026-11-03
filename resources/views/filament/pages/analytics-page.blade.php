<x-filament-panels::page>
    @php
        $m = $this->metrics;
        $courses = $this->topCourses;
        $snapshots = $this->dailySnapshots;
        $bars = $snapshots->reverse()->values();
        $maxGross = max(1, (float) $bars->max('gross_revenue'));
        $maxCourseRevenue = max(1, (float) collect($courses)->max('revenue'));
        $gateways = [
            ['label' => 'Midtrans (QRIS & VA)', 'value' => 60, 'color' => '#4f46e5'],
            ['label' => 'Transfer Bank Manual', 'value' => 30, 'color' => '#10b981'],
            ['label' => 'Xendit Invoice', 'value' => 10, 'color' => '#f59e0b'],
        ];
        $periods = [
            '7_days' => '7 Hari',
            '30_days' => '30 Hari',
            'this_month' => 'Bulan Ini',
            'all_time' => 'Semua Waktu',
        ];
    @endphp

    <style>
        .ana { --ana-card: #ffffff; --ana-line: rgba(15, 23, 42, .08); --ana-muted: #64748b; --ana-text: #0f172a; }
        .dark .ana { --ana-card: #111827; --ana-line: rgba(255, 255, 255, .08); --ana-muted: #94a3b8; --ana-text: #f1f5f9; }
        .ana, .ana * { box-sizing: border-box; }
        .ana { color: var(--ana-text); }

        .ana-toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .ana-seg { display: inline-flex; flex-wrap: wrap; gap: 4px; padding: 4px; border-radius: 999px; background: rgba(100, 116, 139, .12); }
        .ana-seg button { border: 0; background: transparent; padding: 7px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; color: var(--ana-muted); cursor: pointer; transition: all .15s; }
        .ana-seg button:hover { color: var(--ana-text); }
        .ana-seg button.is-active { background: var(--ana-card); color: #4f46e5; box-shadow: 0 1px 3px rgba(0, 0, 0, .12); }
        .dark .ana-seg button.is-active { color: #a5b4fc; }

        .ana-card { background: var(--ana-card); border: 1px solid var(--ana-line); border-radius: 18px; padding: 20px; box-shadow: 0 1px 2px rgba(0, 0, 0, .04); }
        .ana-card h3 { font-size: 14px; font-weight: 700; margin: 0; }
        .ana-card .ana-sub { font-size: 12px; color: var(--ana-muted); margin-top: 2px; }
        .ana-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 16px; }

        .ana-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px; }
        .ana-kpi-top { display: flex; justify-content: space-between; align-items: center; }
        .ana-kpi-label { font-size: 12px; font-weight: 600; color: var(--ana-muted); text-transform: uppercase; letter-spacing: .04em; }
        .ana-kpi-ico { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 6px 14px rgba(0, 0, 0, .12); }
        .ana-kpi-value { font-size: 26px; font-weight: 800; margin-top: 14px; letter-spacing: -.02em; }
        .ana-kpi-foot { font-size: 12px; color: var(--ana-muted); margin-top: 6px; display: flex; align-items: center; gap: 6px; }
        .ana-pill { display: inline-block; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: rgba(16, 185, 129, .12); color: #059669; }

        .ana-main { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 20px; margin-bottom: 20px; }
        .ana-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 20px; margin-bottom: 20px; }
        @media (max-width: 1024px) { .ana-main, .ana-row { grid-template-columns: 1fr; } }

        .ana-bars { display: flex; align-items: flex-end; gap: 8px; height: 230px; padding-top: 8px; border-bottom: 1px dashed var(--ana-line); }
        .ana-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; min-width: 0; }
        .ana-bar { width: 100%; max-width: 44px; border-radius: 10px 10px 4px 4px; background: linear-gradient(180deg, #818cf8, #4f46e5); min-height: 4px; transition: opacity .15s; }
        .ana-bar-wrap:hover .ana-bar { opacity: .75; }
        .ana-bar-label { font-size: 10px; color: var(--ana-muted); margin-top: 6px; white-space: nowrap; }
        .ana-empty { text-align: center; color: var(--ana-muted); font-size: 13px; padding: 60px 0; }

        .ana-donut-wrap { display: flex; flex-direction: column; align-items: center; gap: 18px; }
        .ana-donut { width: 170px; height: 170px; border-radius: 50%; position: relative; }
        .ana-donut::after { content: ''; position: absolute; inset: 34px; border-radius: 50%; background: var(--ana-card); }
        .ana-donut-center { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1; }
        .ana-donut-center b { font-size: 20px; }
        .ana-donut-center span { font-size: 11px; color: var(--ana-muted); }
        .ana-legend { width: 100%; display: flex; flex-direction: column; gap: 10px; }
        .ana-legend-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
        .ana-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .ana-legend-item b { margin-left: auto; }

        .ana-course { padding: 14px 0; border-top: 1px solid var(--ana-line); }
        .ana-course:first-of-type { border-top: 0; padding-top: 0; }
        .ana-course-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
        .ana-course-title { font-size: 14px; font-weight: 700; }
        .ana-course-meta { font-size: 12px; color: var(--ana-muted); margin-top: 4px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .ana-tag { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; background: rgba(100, 116, 139, .12); }
        .ana-course-rev { font-size: 14px; font-weight: 800; color: #059669; white-space: nowrap; }
        .ana-track { height: 6px; border-radius: 999px; background: rgba(100, 116, 139, .15); margin-top: 10px; overflow: hidden; }
        .ana-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #4f46e5, #10b981); }

        .ana-stat-mini { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px; }
        .ana-stat-mini div { background: rgba(100, 116, 139, .08); border-radius: 14px; padding: 12px; text-align: center; }
        .ana-stat-mini b { display: block; font-size: 18px; }
        .ana-stat-mini span { font-size: 11px; color: var(--ana-muted); }

        .ana-table-wrap { overflow-x: auto; margin: 0 -20px -20px; }
        .ana-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .ana-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: var(--ana-muted); padding: 12px 20px; background: rgba(100, 116, 139, .06); border-top: 1px solid var(--ana-line); border-bottom: 1px solid var(--ana-line); white-space: nowrap; }
        .ana-table td { padding: 14px 20px; border-bottom: 1px solid var(--ana-line); white-space: nowrap; }
        .ana-table tr:hover td { background: rgba(100, 116, 139, .05); }
        .ana-table td.money { color: #059669; font-weight: 700; }
    </style>

    <div class="ana">
        {{-- Toolbar --}}
        <div class="ana-toolbar">
            <div class="ana-seg" role="group" aria-label="Rentang periode">
                @foreach($periods as $key => $label)
                    <button type="button"
                        wire:click="$set('period', '{{ $key }}')"
                        class="{{ $period === $key ? 'is-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>

            <x-filament::button type="button" wire:click="refreshSnapshots" icon="bx-refresh" color="gray">
                Sinkronkan Snapshot Hari Ini
            </x-filament::button>
        </div>

        {{-- KPI cards --}}
        <div class="ana-kpis">
            <div class="ana-card">
                <div class="ana-kpi-top">
                    <span class="ana-kpi-label">Total Omzet</span>
                    <span class="ana-kpi-ico" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <x-filament::icon icon="bx-money" class="w-5 h-5" />
                    </span>
                </div>
                <div class="ana-kpi-value">Rp {{ number_format($m['gross_revenue'], 0, ',', '.') }}</div>
                <div class="ana-kpi-foot"><span class="ana-pill">Net</span> Rp {{ number_format($m['net_revenue'], 0, ',', '.') }}</div>
            </div>

            <div class="ana-card">
                <div class="ana-kpi-top">
                    <span class="ana-kpi-label">Member Aktif</span>
                    <span class="ana-kpi-ico" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                        <x-filament::icon icon="bx-id-card" class="w-5 h-5" />
                    </span>
                </div>
                <div class="ana-kpi-value">{{ number_format($m['active_subscribers']) }}</div>
                <div class="ana-kpi-foot">Langganan VIP &amp; Pro Trader</div>
            </div>

            <div class="ana-card">
                <div class="ana-kpi-top">
                    <span class="ana-kpi-label">Murid Baru</span>
                    <span class="ana-kpi-ico" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <x-filament::icon icon="bx-user-plus" class="w-5 h-5" />
                    </span>
                </div>
                <div class="ana-kpi-value">{{ number_format($m['new_students_count']) }}</div>
                <div class="ana-kpi-foot">{{ $m['paid_orders_count'] }} transaksi sukses</div>
            </div>

            <div class="ana-card">
                <div class="ana-kpi-top">
                    <span class="ana-kpi-label">Konversi Order</span>
                    <span class="ana-kpi-ico" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <x-filament::icon icon="bx-pie-chart-alt-2" class="w-5 h-5" />
                    </span>
                </div>
                <div class="ana-kpi-value">{{ $m['conversion_rate'] }}%</div>
                <div class="ana-kpi-foot">Order paid / total invoice</div>
            </div>
        </div>

        {{-- Revenue chart + gateway donut --}}
        <div class="ana-main">
            <div class="ana-card">
                <div class="ana-head">
                    <div>
                        <h3>Tren Omzet Harian</h3>
                        <div class="ana-sub">{{ $bars->count() }} hari terakhir dari snapshot</div>
                    </div>
                </div>

                @if($bars->isEmpty())
                    <div class="ana-empty">Belum ada data snapshot untuk periode ini.</div>
                @else
                    <div class="ana-bars">
                        @foreach($bars as $bar)
                            <div class="ana-bar-wrap" title="{{ $bar->snapshot_date?->format('d M Y') }}: Rp {{ number_format($bar->gross_revenue, 0, ',', '.') }}">
                                <div class="ana-bar" style="height: {{ max(4, round(((float) $bar->gross_revenue / $maxGross) * 100)) }}%;"></div>
                                <span class="ana-bar-label">{{ $bar->snapshot_date?->format('d/m') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="ana-card">
                <div class="ana-head">
                    <div>
                        <h3>Gateway Pembayaran</h3>
                        <div class="ana-sub">Distribusi metode bayar</div>
                    </div>
                </div>

                <div class="ana-donut-wrap">
                    <div class="ana-donut" style="background: conic-gradient({{ $gateways[0]['color'] }} 0 {{ $gateways[0]['value'] }}%, {{ $gateways[1]['color'] }} {{ $gateways[0]['value'] }}% {{ $gateways[0]['value'] + $gateways[1]['value'] }}%, {{ $gateways[2]['color'] }} {{ $gateways[0]['value'] + $gateways[1]['value'] }}% 100%);">
                        <div class="ana-donut-center">
                            <b>{{ number_format($m['paid_orders_count']) }}</b>
                            <span>order paid</span>
                        </div>
                    </div>

                    <div class="ana-legend">
                        @foreach($gateways as $g)
                            <div class="ana-legend-item">
                                <span class="ana-dot" style="background: {{ $g['color'] }};"></span>
                                <span>{{ $g['label'] }}</span>
                                <b>{{ $g['value'] }}%</b>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Top courses + learning activity --}}
        <div class="ana-row">
            <div class="ana-card">
                <div class="ana-head">
                    <div>
                        <h3>Kursus Terpopuler</h3>
                        <div class="ana-sub">Berdasarkan jumlah murid dan omzet</div>
                    </div>
                </div>

                @foreach($courses as $course)
                    <div class="ana-course">
                        <div class="ana-course-top">
                            <div>
                                <div class="ana-course-title">{{ $course['title'] }}</div>
                                <div class="ana-course-meta">
                                    <span class="ana-tag">{{ $course['level'] }}</span>
                                    <span><b>{{ number_format($course['enrollments']) }}</b> murid</span>
                                </div>
                            </div>
                            <div class="ana-course-rev">Rp {{ number_format($course['revenue'], 0, ',', '.') }}</div>
                        </div>
                        <div class="ana-track">
                            <div class="ana-fill" style="width: {{ max(3, round(($course['revenue'] / $maxCourseRevenue) * 100)) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="ana-card">
                <div class="ana-head">
                    <div>
                        <h3>Aktivitas Belajar</h3>
                        <div class="ana-sub">Ringkasan performa murid</div>
                    </div>
                </div>

                <div class="ana-stat-mini">
                    <div><b>92.4%</b><span>Tingkat lolos kuis</span></div>
                    <div><b>88%</b><span>Kehadiran webinar</span></div>
                </div>
                <p class="ana-sub" style="margin-top: 14px;">Angka aktivitas belajar masih ditampilkan dari nilai contoh. Hubungkan ke data kuis dan webinar untuk nilai riil.</p>
            </div>
        </div>

        {{-- Daily snapshot table --}}
        <div class="ana-card" style="padding-bottom: 20px;">
            <div class="ana-head">
                <div>
                    <h3>Riwayat Snapshot Harian</h3>
                    <div class="ana-sub">14 hari terakhir</div>
                </div>
            </div>

            <div class="ana-table-wrap">
                <table class="ana-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Omzet Bruto</th>
                            <th>Order Sukses / Total</th>
                            <th>User Baru</th>
                            <th>Subscriber Aktif</th>
                            <th>Modul Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($snapshots as $snapshot)
                            <tr>
                                <td><b>{{ $snapshot->snapshot_date ? $snapshot->snapshot_date->format('d M Y') : '-' }}</b></td>
                                <td class="money">Rp {{ number_format($snapshot->gross_revenue, 0, ',', '.') }}</td>
                                <td>{{ $snapshot->paid_orders_count }} <span class="ana-sub">/ {{ $snapshot->new_orders_count }}</span></td>
                                <td>+{{ $snapshot->new_users_count }}</td>
                                <td>{{ $snapshot->active_subscribers_count }}</td>
                                <td>{{ $snapshot->lesson_completions_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="ana-empty" style="padding: 32px 0;">Belum ada data snapshot harian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
