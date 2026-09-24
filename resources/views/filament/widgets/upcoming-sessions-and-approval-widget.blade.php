<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-base font-bold text-gray-950 dark:text-white">Upcoming live sessions</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-normal mt-0.5">Next 3 days</p>
            </div>
            <a href="/admin/live-sessions" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                Schedule
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Session 1 --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-950/60 border border-primary-100 dark:border-primary-800/50 flex flex-col items-center justify-center shrink-0">
                    <span class="text-[9px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider">SEP</span>
                    <span class="text-xs font-bold text-primary-700 dark:text-primary-300 -mt-0.5">25</span>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Advanced price action</h4>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Coach Roby · 19:00 WIB</p>
                </div>
            </div>
            <x-filament::badge color="success">
                Ready
            </x-filament::badge>
        </div>

        {{-- Session 2 --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-950/60 border border-primary-100 dark:border-primary-800/50 flex flex-col items-center justify-center shrink-0">
                    <span class="text-[9px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider">SEP</span>
                    <span class="text-xs font-bold text-primary-700 dark:text-primary-300 -mt-0.5">26</span>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Reading the Fed decision</h4>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Coach Sinta · 20:00 WIB</p>
                </div>
            </div>
            <x-filament::badge color="warning">
                No link
            </x-filament::badge>
        </div>

        {{-- Session 3 --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-950/60 border border-primary-100 dark:border-primary-800/50 flex flex-col items-center justify-center shrink-0">
                    <span class="text-[9px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider">SEP</span>
                    <span class="text-xs font-bold text-primary-700 dark:text-primary-300 -mt-0.5">27</span>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">Journal review clinic</h4>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Coach Roby · 15:00 WIB</p>
                </div>
            </div>
            <x-filament::badge color="success">
                Ready
            </x-filament::badge>
        </div>
    </div>

    {{-- Section 2: Awaiting Approval --}}
    <div class="border-t border-gray-100 dark:border-white/10 pt-4 mt-5">
        <h4 class="text-xs font-bold text-gray-900 dark:text-white pb-3">Awaiting approval</h4>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-xs font-semibold text-gray-900 dark:text-white">Order Flow for Beginners</h5>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Coach Sinta · submitted today</p>
                </div>
                <a href="/admin/courses" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                    Review
                </a>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-xs font-semibold text-gray-900 dark:text-white">Gold Trading Playbook</h5>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Coach Roby · 2 days ago</p>
                </div>
                <a href="/admin/courses" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                    Review
                </a>
            </div>
        </div>
    </div>
</x-filament::section>
