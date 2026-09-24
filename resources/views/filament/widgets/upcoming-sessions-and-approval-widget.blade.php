<div class="h-full rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs p-6 flex flex-col justify-between">
    {{-- Section 1: Upcoming Live Sessions --}}
    <div>
        <div class="flex items-center justify-between pb-3">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Upcoming live sessions</h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Next 3 days</p>
            </div>
            <a href="/admin/live-sessions" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">
                Schedule
            </a>
        </div>

        <div class="space-y-3 pt-2">
            {{-- Session 1 --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/50 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[9px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider">SEP</span>
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300 -mt-0.5">25</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Advanced price action</h4>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Coach Roby · 19:00 WIB</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                    Ready
                </span>
            </div>

            {{-- Session 2 --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/50 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[9px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider">SEP</span>
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300 -mt-0.5">26</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Reading the Fed decision</h4>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Coach Sinta · 20:00 WIB</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-800/50">
                    No link
                </span>
            </div>

            {{-- Session 3 --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-100 dark:border-indigo-800/50 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[9px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-wider">SEP</span>
                        <span class="text-xs font-bold text-indigo-700 dark:text-indigo-300 -mt-0.5">27</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Journal review clinic</h4>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500">Coach Roby · 15:00 WIB</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/50">
                    Ready
                </span>
            </div>
        </div>
    </div>

    {{-- Section 2: Awaiting Approval --}}
    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mt-5">
        <h3 class="text-xs font-bold text-gray-900 dark:text-white pb-2.5">Awaiting approval</h3>

        <div class="space-y-3">
            {{-- Item 1 --}}
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Order Flow for Beginners</h4>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Coach Sinta · submitted today</p>
                </div>
                <a href="/admin/courses" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">
                    Review
                </a>
            </div>

            {{-- Item 2 --}}
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Gold Trading Playbook</h4>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Coach Roby · 2 days ago</p>
                </div>
                <a href="/admin/courses" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition">
                    Review
                </a>
            </div>
        </div>
    </div>
</div>
