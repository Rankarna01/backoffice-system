<div class="h-full rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs p-6 flex flex-col justify-between">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Revenue & new members</h3>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Revenue in Rp juta, members per day</p>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mt-3">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-indigo-600"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Revenue</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-sky-400"></span>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">New members</span>
                </div>
            </div>
        </div>

        {{-- Live indicator --}}
        <div class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 font-medium">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Updated 5 s ago</span>
        </div>
    </div>

    {{-- Chart Area --}}
    <div class="mt-6 w-full h-[240px] relative">
        <svg class="w-full h-full" viewBox="0 0 700 240" preserveAspectRatio="none" fill="none">
            <defs>
                <linearGradient id="revenueGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#4F46E5" stop-opacity="0.25"></stop>
                    <stop offset="100%" stop-color="#4F46E5" stop-opacity="0.0"></stop>
                </linearGradient>
                <linearGradient id="membersGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#38BDF8" stop-opacity="0.2"></stop>
                    <stop offset="100%" stop-color="#38BDF8" stop-opacity="0.0"></stop>
                </linearGradient>
            </defs>

            {{-- Grid horizontal lines --}}
            <line x1="40" y1="30" x2="690" y2="30" stroke="#F1F5F9" class="dark:stroke-gray-800" stroke-dasharray="4 4" stroke-width="1"></line>
            <line x1="40" y1="80" x2="690" y2="80" stroke="#F1F5F9" class="dark:stroke-gray-800" stroke-dasharray="4 4" stroke-width="1"></line>
            <line x1="40" y1="130" x2="690" y2="130" stroke="#F1F5F9" class="dark:stroke-gray-800" stroke-dasharray="4 4" stroke-width="1"></line>
            <line x1="40" y1="180" x2="690" y2="180" stroke="#F1F5F9" class="dark:stroke-gray-800" stroke-dasharray="4 4" stroke-width="1"></line>
            <line x1="40" y1="215" x2="690" y2="215" stroke="#E2E8F0" class="dark:stroke-gray-800" stroke-width="1"></line>

            {{-- Y-Axis Labels --}}
            <text x="15" y="34" font-size="11" fill="#94A3B8" class="font-mono">60</text>
            <text x="15" y="84" font-size="11" fill="#94A3B8" class="font-mono">45</text>
            <text x="15" y="134" font-size="11" fill="#94A3B8" class="font-mono">30</text>
            <text x="15" y="184" font-size="11" fill="#94A3B8" class="font-mono">15</text>
            <text x="22" y="218" font-size="11" fill="#94A3B8" class="font-mono">0</text>

            {{-- New members path (Cyan) --}}
            <path d="M40,165 C90,150 140,170 190,135 C240,105 290,120 340,95 C390,75 440,110 490,85 C540,65 590,75 640,45 C665,30 680,35 690,25 L690,215 L40,215 Z" fill="url(#membersGrad)"></path>
            <path d="M40,165 C90,150 140,170 190,135 C240,105 290,120 340,95 C390,75 440,110 490,85 C540,65 590,75 640,45 C665,30 680,35 690,25" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" fill="none"></path>

            {{-- Revenue path (Indigo) --}}
            <path d="M40,195 C90,185 140,190 190,165 C240,140 290,150 340,120 C390,95 440,105 490,70 C540,48 590,60 640,35 C665,22 680,26 690,15 L690,215 L40,215 Z" fill="url(#revenueGrad)"></path>
            <path d="M40,195 C90,185 140,190 190,165 C240,140 290,150 340,120 C390,95 440,105 490,70 C540,48 590,60 640,35 C665,22 680,26 690,15" stroke="#4F46E5" stroke-width="2.5" stroke-linecap="round" fill="none"></path>

            {{-- Interactive high point markers --}}
            <circle cx="690" cy="15" r="4.5" fill="#4F46E5" stroke="#FFFFFF" stroke-width="2"></circle>
            <circle cx="690" cy="25" r="4" fill="#38BDF8" stroke="#FFFFFF" stroke-width="2"></circle>
        </svg>

        {{-- X-Axis Labels --}}
        <div class="flex justify-between pl-10 pr-2 pt-2 text-[11px] text-gray-400 dark:text-gray-500 font-mono">
            <span>01 Sep</span>
            <span>05 Sep</span>
            <span>10 Sep</span>
            <span>15 Sep</span>
            <span>20 Sep</span>
            <span>25 Sep</span>
            <span>30 Sep</span>
        </div>
    </div>
</div>
