<div class="space-y-4">
    <div class="flex items-center justify-between p-3 bg-gray-900 border border-gray-800 rounded-xl text-xs">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-bold text-gray-200">TradingView Official Economic Calendar Widget</span>
        </div>
        <div class="flex items-center gap-2 text-gray-400">
            <span>Tema: <strong class="text-amber-400">{{ strtoupper($config->color_theme ?? 'dark') }}</strong></span>
            <span>•</span>
            <span>Locale: <strong class="text-indigo-400">{{ $config->locale ?? 'en' }}</strong></span>
            <span>•</span>
            <span>Filter: <strong class="text-rose-400">{{ $config->importance_filter ?? '-1,0,1' }}</strong></span>
        </div>
    </div>

    <!-- TradingView Widget Container -->
    <div class="tradingview-widget-container rounded-xl overflow-hidden shadow-2xl border border-gray-800" style="width: 100%; height: {{ is_numeric($config->height ?? 650) ? ($config->height ?? 650).'px' : ($config->height ?? '650px') }}; min-height: 550px;">
        <div class="tradingview-widget-container__widget" style="width: 100%; height: 100%;"></div>
        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-events.js" async>
        {!! json_encode([
            'colorTheme' => $config->color_theme ?? 'dark',
            'isTransparent' => (bool) ($config->is_transparent ?? false),
            'width' => '100%',
            'height' => is_numeric($config->height ?? 650) ? (int) $config->height : 650,
            'locale' => $config->locale ?? 'en',
            'importanceFilter' => $config->importance_filter ?? '-1,0,1',
            'currencyFilter' => is_array($config->currencies ?? null) ? implode(',', $config->currencies) : ($config->currencies ?? 'USD,EUR,GBP,JPY,AUD,CAD,CHF'),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
        </script>
    </div>
</div>
