<div class="h-full rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs p-6 flex flex-col justify-between">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-4">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Latest orders</h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Payments confirmed by webhook</p>
        </div>
        <a href="/admin/orders" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">
            View all
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto -mx-6 px-6">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                    <th class="py-2.5 pr-4 font-medium">Order</th>
                    <th class="py-2.5 px-4 font-medium">Customer</th>
                    <th class="py-2.5 px-4 font-medium">Item</th>
                    <th class="py-2.5 px-4 font-medium">Amount</th>
                    <th class="py-2.5 pl-4 font-medium text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60 text-xs">
                {{-- Row 1 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                    <td class="py-3.5 pr-4 font-medium text-gray-700 dark:text-gray-300">#TE-10482</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                RK
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Randy Karna</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">Smart Money Concept</td>
                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white">Rp 1.490.000</td>
                    <td class="py-3.5 pl-4 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                            Paid
                        </span>
                    </td>
                </tr>

                {{-- Row 2 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                    <td class="py-3.5 pr-4 font-medium text-gray-700 dark:text-gray-300">#TE-10481</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-cyan-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                AN
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Ayu Nirmala</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">Pro plan · 3 months</td>
                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white">Rp 899.000</td>
                    <td class="py-3.5 pl-4 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/50">
                            Pending
                        </span>
                    </td>
                </tr>

                {{-- Row 3 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                    <td class="py-3.5 pr-4 font-medium text-gray-700 dark:text-gray-300">#TE-10480</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                BS
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Bima Saputra</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">Forex Basics</td>
                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white">Rp 349.000</td>
                    <td class="py-3.5 pl-4 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                            Paid
                        </span>
                    </td>
                </tr>

                {{-- Row 4 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                    <td class="py-3.5 pr-4 font-medium text-gray-700 dark:text-gray-300">#TE-10479</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                DL
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Dewi Lestari</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">Pro plan · 12 months</td>
                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white">Rp 2.990.000</td>
                    <td class="py-3.5 pl-4 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800/50">
                            Refunded
                        </span>
                    </td>
                </tr>

                {{-- Row 5 --}}
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                    <td class="py-3.5 pr-4 font-medium text-gray-700 dark:text-gray-300">#TE-10478</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                                FH
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Fikri Hasan</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-4 text-gray-600 dark:text-gray-400">Risk Management</td>
                    <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white">Rp 499.000</td>
                    <td class="py-3.5 pl-4 text-right">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-800/50">
                            Expired
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
