<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $certificate->certificate_number }} - {{ $certificate->student_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f1f5f9;
        }
        .font-cinzel {
            font-family: 'Cinzel', serif;
        }
        .font-serif-luxury {
            font-family: 'Playfair Display', serif;
        }
        .gold-gradient-text {
            background: linear-gradient(135deg, #fce8a6 0%, #d4af37 50%, #aa771c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-border {
            border-image: linear-gradient(135deg, #d4af37, #fce8a6, #aa771c, #d4af37) 1;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: #0f172a !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .certificate-container {
                box-shadow: none !important;
                border: 4px solid #b8860b !important;
                background: white !important;
                color: #0f172a !important;
                width: 100vw !important;
                height: 100vh !important;
                page-break-after: always;
            }
            .gold-gradient-text {
                background: none !important;
                -webkit-text-fill-color: #996515 !important;
                color: #996515 !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">

    {{-- Floating Action Toolbar (Hidden during print) --}}
    <div class="no-print w-full max-w-5xl flex items-center justify-between pb-6 gap-4">
        <a href="{{ url('/admin/certificates') }}" class="flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Panel
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs flex items-center gap-2 shadow-lg transition active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Download PDF
            </button>
            <a href="{{ url('/verify/' . $certificate->certificate_number) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-medium text-xs flex items-center gap-1.5 border border-slate-700 transition">
                <span>Verifikasi Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    {{-- Main Luxury Certificate Canvas (A4 Landscape Aspect Ratio) --}}
    <div class="certificate-container relative w-full max-w-5xl bg-[#0f1424] border-4 border-amber-500/40 rounded-3xl p-8 sm:p-14 shadow-2xl overflow-hidden flex flex-col justify-between" style="aspect-ratio: 1.414 / 1;">
        
        {{-- Geometric Border Inset --}}
        <div class="absolute inset-4 border border-amber-500/20 rounded-2xl pointer-events-none"></div>
        <div class="absolute inset-6 border-2 border-dashed border-amber-500/30 rounded-xl pointer-events-none"></div>

        {{-- Corner Ornaments --}}
        <div class="absolute top-6 left-6 w-8 h-8 border-t-4 border-l-4 border-amber-400 rounded-tl pointer-events-none"></div>
        <div class="absolute top-6 right-6 w-8 h-8 border-t-4 border-r-4 border-amber-400 rounded-tr pointer-events-none"></div>
        <div class="absolute bottom-6 left-6 w-8 h-8 border-b-4 border-l-4 border-amber-400 rounded-bl pointer-events-none"></div>
        <div class="absolute bottom-6 right-6 w-8 h-8 border-b-4 border-r-4 border-amber-400 rounded-br pointer-events-none"></div>

        {{-- Background Seal Watermark --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L1 21h22L12 2zm0 3.84L19.53 19H4.47L12 5.84z"/>
            </svg>
        </div>

        {{-- Certificate Header --}}
        <div class="relative z-10 text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold tracking-[0.25em] uppercase font-cinzel">
                Official Credential of Achievement
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-wider uppercase font-cinzel gold-gradient-text">
                TradingEdu Academy Global
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 tracking-wide font-medium">
                Pusat Edukasi Finansial & Analisis Teknikal Pasar Modal Terakreditasi
            </p>
        </div>

        {{-- Certificate Recipient and Course Details --}}
        <div class="relative z-10 text-center my-auto py-6 space-y-4">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 font-semibold font-cinzel">
                Sertifikat Ini Diberikan Kepada:
            </p>

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif-luxury font-extrabold gold-gradient-text tracking-wide drop-shadow-sm">
                {{ $certificate->student_name ?? $certificate->user?->name }}
            </h2>

            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Telah berhasil menyelesaikan seluruh kurikulum pembelajaran teori, simulasi praktik live market, serta memenuhi standar kelulusan pengujian kompetensi pada program kursus:
            </p>

            <div class="inline-block px-6 py-3 rounded-2xl bg-slate-900/90 border border-amber-500/40 shadow-inner">
                <h3 class="text-base sm:text-xl font-bold text-white font-cinzel tracking-wide">
                    {{ $certificate->course_title ?? $certificate->course?->title }}
                </h3>
                <span class="text-xs text-amber-400 font-medium">
                    Level: {{ ucfirst($certificate->course?->level ?? 'intermediate') }} · Mentor: {{ $certificate->course?->mentor?->name ?? 'TradingEdu Master Mentor' }}
                </span>
            </div>
        </div>

        {{-- Certificate Signatures & Security Footer --}}
        <div class="relative z-10 pt-6 border-t border-amber-500/20 grid grid-cols-3 gap-4 items-end text-xs">
            {{-- Left Signature --}}
            <div class="text-center space-y-1">
                <div class="h-10 flex items-center justify-center">
                    <span class="font-serif-luxury text-lg italic text-amber-400/80">Dr. Jonathan K., M.Fin</span>
                </div>
                <div class="w-36 mx-auto border-b border-slate-600"></div>
                <p class="font-bold text-slate-200 text-[11px] uppercase tracking-wider font-cinzel">Head of Curriculum</p>
                <p class="text-[10px] text-slate-400">TradingEdu Academy</p>
            </div>

            {{-- Center Official Golden Badge / Seal --}}
            <div class="flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-gradient-to-tr from-amber-600 via-amber-400 to-yellow-200 p-0.5 shadow-xl flex items-center justify-center">
                    <div class="w-full h-full rounded-full bg-slate-950 flex flex-col items-center justify-center text-center p-1 border border-amber-300">
                        <span class="text-[8px] font-black uppercase text-amber-400 font-cinzel leading-none">VERIFIED</span>
                        <svg class="w-4 h-4 text-amber-400 my-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="text-[7px] text-slate-300 font-mono leading-none">ORIGINAL</span>
                    </div>
                </div>
                <span class="text-[10px] text-amber-400/90 font-mono mt-1 font-semibold">
                    {{ $certificate->certificate_number }}
                </span>
            </div>

            {{-- Right Signature & Verification Metadata --}}
            <div class="text-center space-y-1">
                <div class="h-10 flex items-center justify-center">
                    <span class="font-serif-luxury text-lg italic text-amber-400/80">{{ $certificate->course?->mentor?->name ?? 'Master Trader' }}</span>
                </div>
                <div class="w-36 mx-auto border-b border-slate-600"></div>
                <p class="font-bold text-slate-200 text-[11px] uppercase tracking-wider font-cinzel">Master Mentor</p>
                <p class="text-[10px] text-slate-400">Diterbitkan: {{ $certificate->issued_at?->format('d F Y') ?? now()->format('d F Y') }}</p>
            </div>
        </div>

    </div>

</body>
</html>
