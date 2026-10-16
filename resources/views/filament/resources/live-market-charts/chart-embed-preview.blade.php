<div
    x-data="{
        selectedSymbol: '{{ $symbol ?? 'OANDA:XAUUSD' }}',
        selectedInterval: '{{ $interval ?? '60' }}',
        theme: '{{ $theme ?? 'dark' }}',
        init() {
            this.renderChart();
        },
        changeSymbol(newSymbol) {
            this.selectedSymbol = newSymbol;
            this.renderChart();
        },
        renderChart() {
            this.$nextTick(() => {
                const container = this.$refs.chartContainer;
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
                script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
                script.async = true;

                const params = {
                    'autosize': true,
                    'symbol': this.selectedSymbol,
                    'interval': this.selectedInterval,
                    'timezone': 'Asia/Jakarta',
                    'theme': this.theme,
                    'style': '1',
                    'locale': 'id',
                    'enable_publishing': false,
                    'allow_symbol_change': true,
                    'calendar': false,
                    'support_host': 'https://www.tradingview.com',
                    'hide_side_toolbar': false
                };

                script.innerHTML = JSON.stringify(params);
                widgetDiv.appendChild(script);
                container.appendChild(widgetDiv);
            });
        }
    }"
    class="space-y-4"
>
    <!-- Top Bar Controls -->
    <div class="flex flex-wrap items-center justify-between p-3 bg-gray-900 border border-gray-800 rounded-xl gap-3 text-xs">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="font-bold text-gray-200">TradingView Advanced Real-Time Chart</span>
            <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 font-semibold uppercase tracking-wider text-[10px]">Live Data</span>
        </div>

        <!-- Quick Switcher Pairs (Forex & Crypto) -->
        <div class="flex flex-wrap items-center gap-1.5">
            <button
                type="button"
                @click="changeSymbol('OANDA:XAUUSD')"
                :class="selectedSymbol === 'OANDA:XAUUSD' ? 'bg-amber-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
                class="px-2.5 py-1 rounded transition text-xs"
            >
                XAU/USD (Emas)
            </button>
            <button
                type="button"
                @click="changeSymbol('BINANCE:BTCUSDT')"
                :class="selectedSymbol === 'BINANCE:BTCUSDT' ? 'bg-amber-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
                class="px-2.5 py-1 rounded transition text-xs"
            >
                BTC/USDT
            </button>
            <button
                type="button"
                @click="changeSymbol('BINANCE:ETHUSDT')"
                :class="selectedSymbol === 'BINANCE:ETHUSDT' ? 'bg-amber-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
                class="px-2.5 py-1 rounded transition text-xs"
            >
                ETH/USDT
            </button>
            <button
                type="button"
                @click="changeSymbol('FX:EURUSD')"
                :class="selectedSymbol === 'FX:EURUSD' ? 'bg-amber-500 text-gray-950 font-bold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
                class="px-2.5 py-1 rounded transition text-xs"
            >
                EUR/USD
            </button>
        </div>
    </div>

    <!-- Chart Container -->
    <div
        x-ref="chartContainer"
        class="rounded-xl overflow-hidden shadow-2xl border border-gray-800 bg-gray-950"
        style="width: 100%; height: 600px;"
    >
        <div class="flex flex-col items-center justify-center h-full text-gray-500 text-xs gap-2">
            <div class="w-6 h-6 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            <span>Memuat TradingView Chart XAU/USD...</span>
        </div>
    </div>
</div>
