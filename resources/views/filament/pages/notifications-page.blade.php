<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Siaran Notifikasi</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($this->stats['total_broadcasts']) }}</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-950/40 text-blue-600 rounded-lg">
                    <x-filament::icon icon="bx-broadcast" class="w-6 h-6" />
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Siaran Terkirim</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($this->stats['sent_broadcasts']) }}</p>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 rounded-lg">
                    <x-filament::icon icon="bx-check-double" class="w-6 h-6" />
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Jangkauan Penerima</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ number_format($this->stats['total_recipients']) }}</p>
                </div>
                <div class="p-3 bg-purple-50 dark:bg-purple-950/40 text-purple-600 rounded-lg">
                    <x-filament::icon icon="bx-group" class="w-6 h-6" />
                </div>
            </div>

            <div class="p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-rata Dibaca (Read Rate)</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $this->stats['avg_read_rate'] }}%</p>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 rounded-lg">
                    <x-filament::icon icon="bx-show" class="w-6 h-6" />
                </div>
            </div>
        </div>

        <!-- Toolbar: Filters & Compose Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari judul atau isi notifikasi..."
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none w-64"
                />

                <select
                    wire:model.live="statusFilter"
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
                >
                    <option value="all">Semua Status</option>
                    <option value="sent">Terkirim (Sent)</option>
                    <option value="scheduled">Terjadwal (Scheduled)</option>
                    <option value="draft">Draf (Draft)</option>
                    <option value="cancelled">Dibatalkan (Cancelled)</option>
                </select>

                <select
                    wire:model.live="typeFilter"
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
                >
                    <option value="all">Semua Tipe</option>
                    <option value="announcement">Pengumuman (Announcement)</option>
                    <option value="webinar">Webinar / Live</option>
                    <option value="signal">Sinyal Trading</option>
                    <option value="promo">Promo & Diskon</option>
                    <option value="system">Pembaruan Sistem</option>
                </select>
            </div>

            <x-filament::button
                type="button"
                wire:click="openCompose"
                icon="bx-plus"
            >
                Buat Notifikasi Siaran
            </x-filament::button>
        </div>

        <!-- Broadcast Notifications List -->
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($this->broadcasts as $item)
                    <div class="p-5 hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                @php
                                    $typeColors = [
                                        'announcement' => 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300',
                                        'webinar' => 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300',
                                        'signal' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                        'promo' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                        'system' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                    ];
                                    $statusColors = [
                                        'sent' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20',
                                        'scheduled' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20',
                                        'draft' => 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/20',
                                        'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20',
                                    ];
                                @endphp
                                <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded {{ $typeColors[$item->type] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $item->type }}
                                </span>
                                <span class="text-[11px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $statusColors[$item->status] ?? '' }}">
                                    {{ $item->status }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <x-filament::icon icon="bx-target-lock" class="w-3.5 h-3.5" />
                                    Target: <strong class="text-gray-700 dark:text-gray-300 font-medium">{{ str_replace('_', ' ', $item->target_audience) }}</strong>
                                </span>
                            </div>

                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $item->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $item->body }}</p>

                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 pt-1">
                                <span>Saluran: <strong>{{ implode(', ', $item->channels ?: ['in_app']) }}</strong></span>
                                <span>•</span>
                                <span>Waktu: <strong>{{ $item->sent_at ? $item->sent_at->format('d M Y H:i') : ($item->scheduled_at ? 'Jadwal: ' . $item->scheduled_at->format('d M Y H:i') : 'Draf') }}</strong></span>
                            </div>
                        </div>

                        <!-- Metrics & Actions -->
                        <div class="flex items-center gap-6 justify-between md:justify-end">
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Jangkauan & Baca</p>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ number_format($item->read_count) }} / {{ number_format($item->total_recipients) }}
                                    <span class="text-xs font-normal text-emerald-600">({{ $item->read_rate }}%)</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($item->status !== 'sent')
                                    <button
                                        wire:click="sendNow({{ $item->id }})"
                                        class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 transition"
                                        title="Kirim Sekarang"
                                    >
                                        Kirim Sekarang
                                    </button>
                                @endif

                                @if($item->status === 'scheduled')
                                    <button
                                        wire:click="cancelBroadcast({{ $item->id }})"
                                        class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 dark:bg-amber-950/40 transition"
                                        title="Batalkan Jadwal"
                                    >
                                        Batalkan
                                    </button>
                                @endif

                                <button
                                    wire:click="deleteBroadcast({{ $item->id }})"
                                    wire:confirm="Yakin ingin menghapus siaran notifikasi ini?"
                                    class="p-1.5 text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition"
                                    title="Hapus"
                                >
                                    <x-filament::icon icon="bx-trash" class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <x-filament::icon icon="bx-bell-off" class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada siaran notifikasi</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gunakan tombol "Buat Notifikasi Siaran" untuk menyapa dan mengabarkan murid.</p>
                    </div>
                @endforelse
            </div>

            @if($this->broadcasts->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $this->broadcasts->links() }}
                </div>
            @endif
        </div>

        <!-- Compose Modal -->
        @if($isComposeOpen)
            <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl max-w-xl w-full p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            <x-filament::icon icon="bx-broadcast" class="w-5 h-5 text-primary-600" />
                            Buat Siaran Notifikasi Baru
                        </h3>
                        <button wire:click="closeCompose" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <x-filament::icon icon="bx-x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Judul Siaran</label>
                            <input
                                type="text"
                                wire:model="new_title"
                                placeholder="Contoh: Webinar Trading Emas Malam Ini..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none"
                            />
                            @error('new_title') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Isi Pesan Notifikasi</label>
                            <textarea
                                wire:model="new_body"
                                rows="4"
                                placeholder="Tuliskan detail pengumuman atau instruksi bagi murid..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none"
                            ></textarea>
                            @error('new_body') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Tipe Pesan</label>
                                <select
                                    wire:model="new_type"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
                                >
                                    <option value="announcement">Pengumuman (Announcement)</option>
                                    <option value="webinar">Webinar / Live Session</option>
                                    <option value="signal">Sinyal Trading</option>
                                    <option value="promo">Promo & Voucher Diskon</option>
                                    <option value="system">Pembaruan Sistem</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Target Audiens</label>
                                <select
                                    wire:model="new_target_audience"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
                                >
                                    <option value="all">Semua Member Platform</option>
                                    <option value="subscribers">Pelanggan Aktif (VIP / Pro)</option>
                                    <option value="free_members">Member Akun Gratis</option>
                                    <option value="course_students">Murid Pembeli Kursus</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">URL Tujuan / Tombol Tindakan (Opsional)</label>
                            <input
                                type="text"
                                wire:model="new_action_url"
                                placeholder="/live-sessions atau https://..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none"
                            />
                        </div>

                        <div class="pt-2 border-t border-gray-200 dark:border-gray-800 space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="new_schedule_enabled" class="rounded text-primary-600 focus:ring-primary-500" />
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Jadwalkan Pengiriman Otomatis</span>
                            </label>

                            @if($new_schedule_enabled)
                                <input
                                    type="datetime-local"
                                    wire:model="new_scheduled_at"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500"
                                />
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <x-filament::button color="gray" wire:click="closeCompose">
                            Batal
                        </x-filament::button>
                        <x-filament::button color="gray" wire:click="saveBroadcast(false)">
                            Simpan Draf
                        </x-filament::button>
                        <x-filament::button wire:click="saveBroadcast(true)" icon="bx-send">
                            Kirim Sekarang
                        </x-filament::button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>