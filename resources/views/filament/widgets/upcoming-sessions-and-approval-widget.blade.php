<x-filament::section>
    <x-slot name="heading">
        <div class="flex items-center justify-between w-full">
            <div>
                <span class="text-sm font-bold text-gray-950 dark:text-white">Jadwal Live Session</span>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-normal mt-0.5">Sesi webinar & bimbingan trading</p>
            </div>
            <a href="/admin/live-sessions" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                Kelola
            </a>
        </div>
    </x-slot>

    <div class="space-y-3.5">
        @forelse($sessions ?? [] as $session)
            @php
                $scheduledAt = $session->scheduled_at ?? now();
                $monthStr = strtoupper($scheduledAt->format('M'));
                $dayStr = $scheduledAt->format('d');
                $timeStr = $scheduledAt->format('H:i') . ' WIB';
                $mentorName = $session->mentor?->user?->name ?? 'Coach Mentor';
                $hasLink = !empty($session->join_url);
            @endphp
            <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-950/60 border border-primary-200 dark:border-primary-800/50 flex flex-col items-center justify-center shrink-0">
                        <span class="text-[9px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wider">{{ $monthStr }}</span>
                        <span class="text-xs font-extrabold text-primary-700 dark:text-primary-300 -mt-0.5">{{ $dayStr }}</span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $session->title }}</h4>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $mentorName }} · {{ $timeStr }}</p>
                    </div>
                </div>
                <div class="shrink-0 ml-2">
                    @if($hasLink)
                        <x-filament::badge color="success" size="sm">
                            Siap
                        </x-filament::badge>
                    @else
                        <x-filament::badge color="warning" size="sm">
                            Belum Ada Link
                        </x-filament::badge>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-xs text-gray-400">
                Belum ada jadwal sesi live terdekat.
            </div>
        @endforelse
    </div>

    {{-- Section 2: Kursus & Materi --}}
    <div class="border-t border-gray-100 dark:border-white/10 pt-4 mt-4">
        <div class="flex items-center justify-between pb-2.5">
            <h4 class="text-xs font-bold text-gray-900 dark:text-white">Review Kursus & Materi</h4>
            <a href="/admin/courses" class="text-[11px] font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                Katalog Kursus
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($pendingCourses ?? [] as $course)
                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    <div class="min-w-0 flex-1 mr-2">
                        <h5 class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $course->title }}</h5>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">
                            {{ $course->level ?? 'Semua Level' }} · {{ $course->lessons_count ?? 0 }} Materi
                        </p>
                    </div>
                    <a href="/admin/courses/{{ $course->id }}/edit" class="text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline shrink-0">
                        Edit
                    </a>
                </div>
            @empty
                <p class="text-[11px] text-gray-400 py-2">Semua materi telah tersinkronisasi.</p>
            @endforelse
        </div>
    </div>
</x-filament::section>
