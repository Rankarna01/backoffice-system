<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-base font-bold text-gray-950 dark:text-white">Latest orders</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5">Payments confirmed by webhook</p>
            </div>
            <a href="/admin/orders" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                View all
            </a>
        </div>
    </x-slot>

    <div class="overflow-x-auto -mx-6 -mb-6">
        <table class="w-full text-left text-xs divide-y divide-gray-200 dark:divide-white/10">
            <thead class="bg-gray-50 dark:bg-white/5">
                <tr class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <th class="py-3 px-6">Order</th>
                    <th class="py-3 px-6">Customer</th>
                    <th class="py-3 px-6">Item</th>
                    <th class="py-3 px-6">Amount</th>
                    <th class="py-3 px-6 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                {{-- Row 1 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition">
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">#TE-10482</td>
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                RK
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">Randy Karna</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-600 dark:text-gray-300">Smart Money Concept</td>
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">Rp 1.490.000</td>
                    <td class="py-3 px-6 text-right">
                        <x-filament::badge color="success">
                            Paid
                        </x-filament::badge>
                    </td>
                </tr>

                {{-- Row 2 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition">
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">#TE-10481</td>
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                AN
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">Ayu Nirmala</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-600 dark:text-gray-300">Pro plan · 3 months</td>
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">Rp 899.000</td>
                    <td class="py-3 px-6 text-right">
                        <x-filament::badge color="warning">
                            Pending
                        </x-filament::badge>
                    </td>
                </tr>

                {{-- Row 3 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition">
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">#TE-10480</td>
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                BS
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">Bima Saputra</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-600 dark:text-gray-300">Forex Basics</td>
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">Rp 349.000</td>
                    <td class="py-3 px-6 text-right">
                        <x-filament::badge color="success">
                            Paid
                        </x-filament::badge>
                    </td>
                </tr>

                {{-- Row 4 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition">
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">#TE-10479</td>
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                DL
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">Dewi Lestari</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-600 dark:text-gray-300">Pro plan · 12 months</td>
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">Rp 2.990.000</td>
                    <td class="py-3 px-6 text-right">
                        <x-filament::badge color="info">
                            Refunded
                        </x-filament::badge>
                    </td>
                </tr>

                {{-- Row 5 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition">
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">#TE-10478</td>
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                FH
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">Fikri Hasan</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-gray-600 dark:text-gray-300">Risk Management</td>
                    <td class="py-3 px-6 font-medium text-gray-900 dark:text-white">Rp 499.000</td>
                    <td class="py-3 px-6 text-right">
                        <x-filament::badge color="danger">
                            Expired
                        </x-filament::badge>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::section>
