<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds for Course Lessons.
     */
    public function run(): void
    {
        $courseSmc = Course::where('slug', 'smart-money-concepts-mastery')->first();
        $courseForex = Course::where('slug', 'forex-trading-basics-beginners')->first();

        // 1. Lessons for SMC Course Modules
        if ($courseSmc) {
            $smcMod1 = Module::where('course_id', $courseSmc->id)->where('sort_order', 1)->first();
            if ($smcMod1) {
                $lessonsMod1 = [
                    [
                        'title' => 'Mengapa 95% Retail Trader Merugi di Pasar Finansial',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 900,
                        'is_preview' => true,
                        'is_required' => true,
                        'sort_order' => 1,
                        'status' => 'published',
                        'content' => "Pelajaran pengantar ini membongkar psikologi pasar dan bagaimana bank sentral serta institusi besar memanfaatkan likuiditas retail untuk mengisi posisi order mereka.",
                    ],
                    [
                        'title' => 'Siklus Likuiditas: Akumulasi, Manipulasi, dan Distribusi',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1200,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 2,
                        'status' => 'published',
                        'content' => "Pelajari fase AMD (Accumulation, Manipulation, Distribution) model Power of 3 (PO3) untuk mendeteksi kapan institusi mulai bergerak.",
                    ],
                    [
                        'title' => 'Ringkasan & Glosarium Istilah Smart Money Concepts',
                        'type' => 'article',
                        'duration_seconds' => 480,
                        'is_preview' => true,
                        'is_required' => true,
                        'sort_order' => 3,
                        'status' => 'published',
                        'content' => "Daftar istilah kunci: BOS (Break of Structure), CHoCH (Change of Character), FVG (Fair Value Gap), OB (Order Block), BSL (Buy-Side Liquidity), SSL (Sell-Side Liquidity).",
                    ],
                ];

                foreach ($lessonsMod1 as $les) {
                    Lesson::updateOrCreate(
                        [
                            'course_id' => $courseSmc->id,
                            'module_id' => $smcMod1->id,
                            'title' => $les['title'],
                        ],
                        array_merge($les, [
                            'uuid' => (string) Str::uuid(),
                            'slug' => Str::slug($les['title']),
                        ])
                    );
                }
            }

            $smcMod2 = Module::where('course_id', $courseSmc->id)->where('sort_order', 2)->first();
            if ($smcMod2) {
                $lessonsMod2 = [
                    [
                        'title' => 'Struktur Pasar Primer vs Secondary (Swing High & Low Valid)',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1080,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 1,
                        'status' => 'published',
                        'content' => "Identifikasi swing high dan swing low yang sah berdasarkan pengambilan likuiditas minor (inducement sweep).",
                    ],
                    [
                        'title' => 'Membedakan Break of Structure (BOS) Asli vs Fakeout',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1350,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 2,
                        'status' => 'published',
                        'content' => "Aturan penutupan body candle vs sumbu (wick) saat menentukan apakah BOS telah terkonfirmasi atau hanya liquidity grab.",
                    ],
                    [
                        'title' => 'Change of Character (CHoCH) Sebagai Sinyal Awal Pembalikan Tren',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1260,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 3,
                        'status' => 'published',
                        'content' => "Langkah awal mengantisipasi pergeseran tren pasar dari bullish ke bearish atau sebaliknya dengan filter time frame yang benar.",
                    ],
                ];

                foreach ($lessonsMod2 as $les) {
                    Lesson::updateOrCreate(
                        [
                            'course_id' => $courseSmc->id,
                            'module_id' => $smcMod2->id,
                            'title' => $les['title'],
                        ],
                        array_merge($les, [
                            'uuid' => (string) Str::uuid(),
                            'slug' => Str::slug($les['title']),
                        ])
                    );
                }
            }

            $smcMod4 = Module::where('course_id', $courseSmc->id)->where('sort_order', 4)->first();
            if ($smcMod4) {
                $lessonsMod4 = [
                    [
                        'title' => 'Anatomi Order Block yang Belum Ter-mitigasi (Fresh OB)',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1440,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 1,
                        'status' => 'published',
                        'content' => "Kriteria Order Block dengan probabilitas tinggi: adanya displacement candle yang agresif, pembentukan FVG, dan sweep likuiditas lawan.",
                    ],
                    [
                        'title' => 'Fair Value Gap (FVG) & Imbalance Inefficiencies',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 1140,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 2,
                        'status' => 'published',
                        'content' => "Mengukur area inefisiensi harga pada chart 3-candle dan menggunakan Consequent Encroachment (50% level) untuk entry presisi.",
                    ],
                ];

                foreach ($lessonsMod4 as $les) {
                    Lesson::updateOrCreate(
                        [
                            'course_id' => $courseSmc->id,
                            'module_id' => $smcMod4->id,
                            'title' => $les['title'],
                        ],
                        array_merge($les, [
                            'uuid' => (string) Str::uuid(),
                            'slug' => Str::slug($les['title']),
                        ])
                    );
                }
            }
        }

        // 2. Lessons for Forex Course Modules
        if ($courseForex) {
            $forexMod1 = Module::where('course_id', $courseForex->id)->where('sort_order', 1)->first();
            if ($forexMod1) {
                $lessonsForex1 = [
                    [
                        'title' => 'Mengenal Pasangan Mata Uang Major, Minor, dan Cross',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 720,
                        'is_preview' => true,
                        'is_required' => true,
                        'sort_order' => 1,
                        'status' => 'published',
                        'content' => "Pengenalan base currency dan quote currency pada pair forex terpopuler: EUR/USD, GBP/USD, USD/JPY.",
                    ],
                    [
                        'title' => 'Karakteristik Jam Perdagangan Sesi London dan New York',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 840,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 2,
                        'status' => 'published',
                        'content' => "Waktu overlap sesi perdagangan dengan likuiditas dan volatilitas tertinggi bagi intraday trader.",
                    ],
                ];

                foreach ($lessonsForex1 as $les) {
                    Lesson::updateOrCreate(
                        [
                            'course_id' => $courseForex->id,
                            'module_id' => $forexMod1->id,
                            'title' => $les['title'],
                        ],
                        array_merge($les, [
                            'uuid' => (string) Str::uuid(),
                            'slug' => Str::slug($les['title']),
                        ])
                    );
                }
            }

            $forexMod2 = Module::where('course_id', $courseForex->id)->where('sort_order', 2)->first();
            if ($forexMod2) {
                $lessonsForex2 = [
                    [
                        'title' => 'Panduan Menghitung Pip Value dan Lot Size Ideal',
                        'type' => 'video',
                        'video_provider' => 'youtube',
                        'video_ref' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'duration_seconds' => 960,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 1,
                        'status' => 'published',
                        'content' => "Rumus dasar menghitung risiko per trade maksimal 1-2% dari total modal balance.",
                    ],
                    [
                        'title' => 'Lembar Kerja Kalkulator Lot & Jurnal Trading Harian',
                        'type' => 'file',
                        'duration_seconds' => 300,
                        'is_preview' => false,
                        'is_required' => true,
                        'sort_order' => 2,
                        'status' => 'published',
                        'content' => "Gunakan file spreadsheet terlampir pada modul untuk menghitung lot size otomatis berdasarkan jarak stop loss.",
                    ],
                ];

                foreach ($lessonsForex2 as $les) {
                    Lesson::updateOrCreate(
                        [
                            'course_id' => $courseForex->id,
                            'module_id' => $forexMod2->id,
                            'title' => $les['title'],
                        ],
                        array_merge($les, [
                            'uuid' => (string) Str::uuid(),
                            'slug' => Str::slug($les['title']),
                        ])
                    );
                }
            }
        }
    }
}
