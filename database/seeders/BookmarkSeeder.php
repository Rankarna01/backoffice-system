<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Bookmark;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) return;

        $courses = Course::take(3)->get();
        $lessons = Lesson::take(5)->get();

        foreach ($users as $user) {
            // Seed 1 saved course
            if ($courses->isNotEmpty()) {
                Bookmark::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bookmarkable_type' => 'course',
                        'bookmarkable_id' => $courses->first()->id,
                    ],
                    [
                        'note' => 'Target selesai modul ini sebelum akhir bulan untuk persiapan evaluasi portofolio.',
                    ]
                );
            }

            // Seed 2 saved lessons
            if ($lessons->count() >= 2) {
                Bookmark::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bookmarkable_type' => 'lesson',
                        'bookmarkable_id' => $lessons[0]->id,
                    ],
                    [
                        'note' => 'Penting: Cek pola candle reject di level 0.618 golden ratio pada menit 14:20.',
                    ]
                );

                Bookmark::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bookmarkable_type' => 'lesson',
                        'bookmarkable_id' => $lessons[1]->id,
                    ],
                    [
                        'note' => 'Rumus perhitungan risk:reward 1:2 wajib diterapkan di setiap order.',
                    ]
                );
            }
        }
    }
}
