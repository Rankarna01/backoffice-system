<div class="h-full rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs p-6 flex flex-col justify-between">
    {{-- Header --}}
    <div>
        <h3 class="text-base font-bold text-gray-900 dark:text-white">Orders by status</h3>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">This period</p>
    </div>

    {{-- Donut Chart with Center Text --}}
    <div class="relative flex items-center justify-center my-4">
        <svg class="w-44 h-44" viewBox="0 0 160 160">
            <g transform="rotate(-90 80 80)">
                {{-- Paid: 68% (Emerald) --}}
                <circle cx="80" cy="80" r="54" fill="none" stroke="#10B981" stroke-width="19" stroke-dasharray="230.72 339.29" stroke-dashoffset="0" stroke-linecap="round"></circle>
                {{-- Pending: 14% (Amber) --}}
                <circle cx="80" cy="80" r="54" fill="none" stroke="#F59E0B" stroke-width="19" stroke-dasharray="47.50 339.29" stroke-dashoffset="-230.72" stroke-linecap="round"></circle>
                {{-- Failed/expired: 7% (Rose) --}}
                <circle cx="80" cy="80" r="54" fill="none" stroke="#EF4444" stroke-width="19" stroke-dasharray="23.75 339.29" stroke-dashoffset="-278.22" stroke-linecap="round"></circle>
                {{-- Refunded: 11% (Indigo) --}}
                <circle cx="80" cy="80" r="54" fill="none" stroke="#6366F1" stroke-width="19" stroke-dasharray="37.32 339.29" stroke-dashoffset="-301.97" stroke-linecap="round"></circle>
            </g>
        </svg>

        {{-- Center Label --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
            <span class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">2.406</span>
            <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">orders</span>
        </div>
    </div>

    {{-- Breakdown List --}}
    <div class="space-y-2.5 pt-2">
        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-medium text-gray-600 dark:text-gray-300">Paid</span>
            </div>
            <span class="font-semibold text-gray-800 dark:text-gray-200">68%</span>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-medium text-gray-600 dark:text-gray-300">Pending</span>
            </div>
            <span class="font-semibold text-gray-800 dark:text-gray-200">14%</span>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <span class="font-medium text-gray-600 dark:text-gray-300">Failed or expired</span>
            </div>
            <span class="font-semibold text-gray-800 dark:text-gray-200">7%</span>
        </div>

        <div class="flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span class="font-medium text-gray-600 dark:text-gray-300">Refunded</span>
            </div>
            <span class="font-semibold text-gray-800 dark:text-gray-200">11%</span>
        </div>
    </div>
</div>
