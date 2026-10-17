<div class="p-6 rounded-2xl bg-slate-900 border-2 border-amber-500/30 text-white relative overflow-hidden shadow-2xl">
    {{-- Decorative Background Watermark & Borders --}}
    <div class="absolute -right-12 -bottom-12 opacity-5 pointer-events-none">
        <svg class="w-72 h-72" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L1 21h22L12 2zm0 3.84L19.53 19H4.47L12 5.84z"/>
        </svg>
    </div>

    {{-- Certificate Header --}}
    <div class="text-center space-y-1 pb-4 border-b border-amber-500/20">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold tracking-widest uppercase">
            Official Certificate of Completion
        </div>
        <h2 class="text-xl font-extrabold tracking-tight text-white pt-2">TradingEdu Academy Global</h2>
        <p class="text-xs text-slate-400">Terakreditasi Kurikulum Analisis Teknikal Finansial Profesional</p>
    </div>

    {{-- Certificate Body --}}
    <div class="py-6 text-center space-y-3">
        <p class="text-xs text-slate-400 uppercase tracking-widest font-medium">Diberikan kepada:</p>
        <h3 class="text-2xl font-black text-amber-400 tracking-wide font-serif">
            {{ $record->student_name ?? $record->user?->name }}
        </h3>
        <p class="text-xs text-slate-300 max-w-md mx-auto pt-1 leading-relaxed">
            Atas dedikasi dan keberhasilan menyelesaikan seluruh kurikulum pembelajaran teori, simulasi praktik, dan evaluasi pengujian:
        </p>
        <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/80 max-w-lg mx-auto">
            <h4 class="text-base font-bold text-white">{{ $record->course_title ?? $record->course?->title }}</h4>
            <span class="text-xs text-amber-400 font-medium">Level: {{ ucfirst($record->course?->level ?? 'intermediate') }}</span>
        </div>
    </div>

    {{-- Certificate Footer Details --}}
    <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-3">
        <div>
            <span class="block text-[11px] text-slate-500">Nomor Sertifikat:</span>
            <span class="font-mono font-bold text-white">{{ $record->certificate_number }}</span>
        </div>
        <div>
            <span class="block text-[11px] text-slate-500">Tanggal Terbit:</span>
            <span class="font-medium text-slate-200">{{ $record->issued_at?->format('d F Y') ?? now()->format('d F Y') }}</span>
        </div>
        <div class="text-right">
            <span class="block text-[11px] text-slate-500">Status Validasi:</span>
            <span class="inline-flex items-center gap-1 font-bold text-emerald-400">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Terverifikasi Resmi
            </span>
        </div>
    </div>
</div>
