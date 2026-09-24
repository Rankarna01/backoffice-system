@props([
    'title' => '',
    'group' => '',
    'description' => '',
    'icon' => 'heroicon-o-cube',
    'stats' => [],
    'features' => [],
    'primaryAction' => null,
])

<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400">
                    <x-filament::icon :icon="$icon" class="h-6 w-6" />
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300">
                            {{ $group }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">• Modul Siap Digunakan</span>
                    </div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                        {{ $title }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                        {{ $description }}
                    </p>
                </div>
            </div>

            @if($primaryAction)
                <div class="shrink-0">
                    <x-filament::button size="md" color="primary">
                        {{ $primaryAction }}
                    </x-filament::button>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Metric Stats Grid (if available) --}}
    @if(!empty($stats))
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($stats as $stat)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $stat['value'] }}</span>
                        @if(isset($stat['change']))
                            <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">{{ $stat['change'] }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Feature Scope & Roadmap Card --}}
    @if(!empty($features))
        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/50 p-6 dark:border-gray-800 dark:bg-gray-900/40">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2 mb-3">
                <x-filament::icon icon="heroicon-o-check-badge" class="h-4 w-4 text-indigo-500" />
                <span>Ruang Lingkup & Kemampuan Fitur:</span>
            </h4>
            <div class="grid gap-2.5 sm:grid-cols-2">
                @foreach($features as $feature)
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                        <span>{{ $feature }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
