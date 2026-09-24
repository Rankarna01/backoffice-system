<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    {{-- Card 1: Revenue --}}
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs flex flex-col justify-between pt-5 px-5 pb-0">
        <div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <div class="flex items-center justify-center w-5 h-5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                        <line x1="2" x2="22" y1="10" y2="10"></line>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Revenue</span>
            </div>
            <div class="mt-2 text-[26px] font-bold text-gray-900 dark:text-white tracking-tight">
                Rp 48,6 jt
            </div>
            <div class="mt-1 flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"></path>
                </svg>
                <span>12,4% vs last period</span>
            </div>
        </div>

        {{-- Sparkline wave (Indigo) --}}
        <div class="mt-4 -mx-5 -mb-0.5">
            <svg class="w-full h-12" viewBox="0 0 300 60" preserveAspectRatio="none" fill="none">
                <defs>
                    <linearGradient id="gradRevenue" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#818CF8" stop-opacity="0.3"></stop>
                        <stop offset="100%" stop-color="#818CF8" stop-opacity="0.0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0,52 C30,48 50,55 80,48 C110,42 130,46 160,32 C190,20 220,38 250,22 C270,12 285,15 300,10 L300,60 L0,60 Z" fill="url(#gradRevenue)"></path>
                <path d="M0,52 C30,48 50,55 80,48 C110,42 130,46 160,32 C190,20 220,38 250,22 C270,12 285,15 300,10" stroke="#818CF8" stroke-width="2" stroke-linecap="round" fill="none"></path>
            </svg>
        </div>
    </div>

    {{-- Card 2: New members --}}
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs flex flex-col justify-between pt-5 px-5 pb-0">
        <div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <div class="flex items-center justify-center w-5 h-5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">New members</span>
            </div>
            <div class="mt-2 text-[26px] font-bold text-gray-900 dark:text-white tracking-tight">
                1.284
            </div>
            <div class="mt-1 flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"></path>
                </svg>
                <span>8,1% vs last period</span>
            </div>
        </div>

        {{-- Sparkline wave (Cyan) --}}
        <div class="mt-4 -mx-5 -mb-0.5">
            <svg class="w-full h-12" viewBox="0 0 300 60" preserveAspectRatio="none" fill="none">
                <defs>
                    <linearGradient id="gradMembers" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#38BDF8" stop-opacity="0.3"></stop>
                        <stop offset="100%" stop-color="#38BDF8" stop-opacity="0.0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0,50 C35,46 60,54 90,44 C120,38 140,42 170,30 C200,24 230,32 260,18 C280,12 290,16 300,14 L300,60 L0,60 Z" fill="url(#gradMembers)"></path>
                <path d="M0,50 C35,46 60,54 90,44 C120,38 140,42 170,30 C200,24 230,32 260,18 C280,12 290,16 300,14" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" fill="none"></path>
            </svg>
        </div>
    </div>

    {{-- Card 3: Active subscriptions --}}
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs flex flex-col justify-between pt-5 px-5 pb-0">
        <div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <div class="flex items-center justify-center w-5 h-5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Active subscriptions</span>
            </div>
            <div class="mt-2 text-[26px] font-bold text-gray-900 dark:text-white tracking-tight">
                3.912
            </div>
            <div class="mt-1 flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5"></path>
                </svg>
                <span>3,6% vs last period</span>
            </div>
        </div>

        {{-- Sparkline wave (Emerald) --}}
        <div class="mt-4 -mx-5 -mb-0.5">
            <svg class="w-full h-12" viewBox="0 0 300 60" preserveAspectRatio="none" fill="none">
                <defs>
                    <linearGradient id="gradSubs" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#34D399" stop-opacity="0.3"></stop>
                        <stop offset="100%" stop-color="#34D399" stop-opacity="0.0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0,52 C40,50 80,48 120,44 C160,40 190,32 230,22 C260,16 280,12 300,8 L300,60 L0,60 Z" fill="url(#gradSubs)"></path>
                <path d="M0,52 C40,50 80,48 120,44 C160,40 190,32 230,22 C260,16 280,12 300,8" stroke="#34D399" stroke-width="2" stroke-linecap="round" fill="none"></path>
            </svg>
        </div>
    </div>

    {{-- Card 4: Course completion --}}
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-xs flex flex-col justify-between pt-5 px-5 pb-0">
        <div>
            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <div class="flex items-center justify-center w-5 h-5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-600 dark:text-gray-300">Course completion</span>
            </div>
            <div class="mt-2 text-[26px] font-bold text-gray-900 dark:text-white tracking-tight">
                64%
            </div>
            <div class="mt-1 flex items-center gap-1 text-xs font-semibold text-rose-500 dark:text-rose-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
                <span>1,2% vs last period</span>
            </div>
        </div>

        {{-- Sparkline wave (Orange/Amber) --}}
        <div class="mt-4 -mx-5 -mb-0.5">
            <svg class="w-full h-12" viewBox="0 0 300 60" preserveAspectRatio="none" fill="none">
                <defs>
                    <linearGradient id="gradCompletion" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#FB923C" stop-opacity="0.3"></stop>
                        <stop offset="100%" stop-color="#FB923C" stop-opacity="0.0"></stop>
                    </linearGradient>
                </defs>
                <path d="M0,54 C40,52 80,46 120,44 C160,42 190,30 230,22 C260,18 280,14 300,10 L300,60 L0,60 Z" fill="url(#gradCompletion)"></path>
                <path d="M0,54 C40,52 80,46 120,44 C160,42 190,30 230,22 C260,18 280,14 300,10" stroke="#FB923C" stroke-width="2" stroke-linecap="round" fill="none"></path>
            </svg>
        </div>
    </div>
</div>
