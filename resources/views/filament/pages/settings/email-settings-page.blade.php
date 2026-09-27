<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            {{ $this->form }}

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                <x-filament::button type="submit" icon="bx-check">
                    Simpan Konfigurasi Email
                </x-filament::button>
            </div>
        </form>

        <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary-50 dark:bg-primary-950/40 text-primary-600 rounded-lg">
                    <x-filament::icon icon="bx-send" class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Uji Coba Pengiriman Email (SMTP Test)</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Verifikasi pengaturan server email Anda dengan mengirimkan email pengujian.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <input
                    type="email"
                    wire:model="test_email_recipient"
                    placeholder="Masukkan alamat email penerima test..."
                    class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:outline-none"
                />
                <x-filament::button
                    type="button"
                    wire:click="sendTestEmail"
                    color="gray"
                    icon="bx-paper-plane"
                >
                    Kirim Email Uji Coba
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>