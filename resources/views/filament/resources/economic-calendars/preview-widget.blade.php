<div
    x-data="{
        init() {
            this.$nextTick(() => {
                const container = this.$refs.widgetWrapper;
                if (!container) return;
                container.innerHTML = '';

                const widgetDiv = document.createElement('div');
                widgetDiv.className = 'tradingview-widget-container';
                widgetDiv.style.width = '100%';
                widgetDiv.style.height = '100%';

                const innerDiv = document.createElement('div');
                innerDiv.className = 'tradingview-widget-container__widget';
                innerDiv.style.width = '100%';
                innerDiv.style.height = '100%';
                widgetDiv.appendChild(innerDiv);

                const script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-events.js';
                script.async = true;

                const params = @js([
                    'colorTheme' => $config->color_theme ?? 'dark',
                    'isTransparent' => (bool) ($config->is_transparent ?? false),
                    'width' => '100%',
                    'height' => is_numeric($config->height ?? 650) ? (int) $config->height : 650,
                    'locale' => $config->locale ?? 'en',
                    'importanceFilter' => $config->importance_filter ?? '-1,0,1',
                    'currencyFilter' => is_array($config->currencies ?? null)
                        ? implode(',', $config->currencies)
                        : ($config->currencies ?? 'USD,EUR,GBP,JPY,AUD,CAD,CHF'),
                ]);

                script.innerHTML = JSON.stringify(params);
                widgetDiv.appendChild(script);
                container.appendChild(widgetDiv);
            });
        }
    }"
    class="space-y-4"
>
    <!-- Header Info Banner -->
    <div class="flex items-center justify-between p-3 bg-gray-900 border border-gray-800 rounded-xl text-xs">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-bold text-gray-200">Official TradingView Economic Calendar</span>
        </div>
        <div class="flex items-center gap-2 text-gray-400">
            <span>Tema: <strong class="text-amber-400">{{ strtoupper($config->color_theme ?? 'dark') }}</strong></span>
            <span>•</span>
            <span>Locale: <strong class="text-indigo-400">{{ $config->locale ?? 'en' }}</strong></span>
            <span>•</span>
            <span>Filter Dampak: <strong class="text-rose-400">{{ $config->importance_filter ?? '-1,0,1' }}</strong></span>
        </div>
    </div>

    <!-- TradingView Widget Mounted via AlpineJS script injection -->
    <div
        x-ref="widgetWrapper"
        class="rounded-xl overflow-hidden shadow-2xl border border-gray-800 bg-gray-950"
        style="width: 100%; min-height: {{ is_numeric($config->height ?? 650) ? ($config->height ?? 650).'px' : ($config->height ?? '650px') }};"
    >
        <div class="flex flex-col items-center justify-center p-12 text-gray-500 text-xs gap-2">
            <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            <span>Memuat widget resmi TradingView...</span>
        </div>
    </div>
</div>
