<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Learning\Models\QuizOption;
use App\Domain\Learning\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds for Quizzes, Questions, and Options.
     */
    public function run(): void
    {
        $courseSmc = Course::where('slug', 'smart-money-concepts-mastery')->first();
        $courseForex = Course::where('slug', 'forex-trading-basics-beginners')->first();

        // -------------------------------------------------------------
        // 1. Quizzes for SMC Course
        // -------------------------------------------------------------
        if ($courseSmc) {
            $smcMod2 = Module::where('course_id', $courseSmc->id)
                ->where('title', 'like', '%Market Structure%')
                ->first();

            $smcMod4 = Module::where('course_id', $courseSmc->id)
                ->where('title', 'like', '%Supply & Demand%')
                ->first();

            // 1.A. Module Quiz: Market Structure
            if ($smcMod2) {
                $quiz1 = Quiz::updateOrCreate(
                    [
                        'course_id' => $courseSmc->id,
                        'title' => 'Kuis Evaluasi: Market Structure, BOS & CHoCH',
                    ],
                    [
                        'module_id' => $smcMod2->id,
                        'instructions' => "Kuis ini menguji kemampuan Anda dalam mengidentifikasi valid swing high/low, perbedaan BOS dan CHoCH, serta penentuan arah tren institutional secara presisi.\n\n- Terdiri dari 4 butir soal pilihan ganda\n- Nilai kelulusan: 75%\n- Waktu pengerjaan: 15 menit.",
                        'passing_score' => 75,
                        'time_limit_minutes' => 15,
                        'max_attempts' => 3,
                        'shuffle_questions' => true,
                        'shuffle_options' => true,
                        'show_answers' => true,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );

                $questionsQ1 = [
                    [
                        'question' => 'Apa perbedaan paling mendasar antara Break of Structure (BOS) dan Change of Character (CHoCH)?',
                        'type' => 'single',
                        'points' => 25,
                        'sort_order' => 1,
                        'explanation' => 'BOS (Break of Structure) mengonfirmasi kelanjutan dari tren yang sedang berjalan (trend continuation), sedangkan CHoCH (Change of Character) mengindikasikan sinyal awal potensi pembalikan tren (trend reversal).',
                        'options' => [
                            ['label' => 'BOS menandakan kelanjutan tren berjalan, sedangkan CHoCH menandakan sinyal awal perubahan arah tren.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'BOS hanya terjadi di timeframe M1, sedangkan CHoCH hanya terjadi pada Daily.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'BOS adalah pergerakan sideways, sedangkan CHoCH adalah penembusan level support.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Kedua istilah tersebut identik dan tidak memiliki perbedaan mekanisme apapun.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'question' => 'Kapan sebuah Swing High baru dianggap terkonfirmasi valid dalam struktur tren bullish?',
                        'type' => 'single',
                        'points' => 25,
                        'sort_order' => 2,
                        'explanation' => 'Dalam kaidah SMC murni, swing high bullish baru valid terkonfirmasi setelah harga melakukan retracement dan berhasil mengambil/sweep Inducement (IDM) terdekat.',
                        'options' => [
                            ['label' => 'Ketika harga berhasil mengambil (sweep/take) Inducement (IDM) terdekat.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Saat indikator RSI menembus angka overbought di atas 70.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Ketika terbentuk candle merah satu kali di puncak chart.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Hanya saat moving average 50 memotong moving average 200.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'question' => 'Dalam penentuan BOS yang valid pada timeframe eksekusi, bagaimana syarat konfirmasi candle yang ideal?',
                        'type' => 'single',
                        'points' => 25,
                        'sort_order' => 3,
                        'explanation' => 'BOS yang valid mensyaratkan body candle ditutup secara penuh (body candle close) di luar swing point. Jika hanya sumbu (wick) yang menembus, kemungkinan besar itu hanyalah liquidity sweep (sweep liquidity).',
                        'options' => [
                            ['label' => 'Body candle harus ditutup secara penuh (full candle close) menembus level swing sebelumnya.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Cukup sumbu (wick) menyentuh level swing point tanpa perlu penutupan body.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Tidak perlu konfirmasi penutupan candle asalkan spread broker rendah.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Candle harus memiliki volume transaksi di bawah rata-rata.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'question' => 'Apa peran utama dari "Inducement (IDM)" dalam kerangka kerja Smart Money Concepts?',
                        'type' => 'single',
                        'points' => 25,
                        'sort_order' => 4,
                        'explanation' => 'Inducement adalah rekayasa likuiditas awal yang memancing trader retail untuk masuk posisi terlalu dini, sehingga menyediakan order kontra bagi institusi.',
                        'options' => [
                            ['label' => 'Titik umpan likuiditas untuk memancing order retail sebelum Smart Money masuk pada titik POI sebenarnya.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Indikator momentum untuk menentukan level take profit utama.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Garis resistance horizontal statis yang tidak pernah tertembus harga.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Waktu pergantian sesi antara London dan New York session.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                ];

                $this->syncQuestionsAndOptions($quiz1, $questionsQ1);
            }

            // 1.B. Module Quiz: Order Block & FVG
            if ($smcMod4) {
                $quiz2 = Quiz::updateOrCreate(
                    [
                        'course_id' => $courseSmc->id,
                        'title' => 'Kuis Analisis: Order Block (OB) & Fair Value Gap (FVG)',
                    ],
                    [
                        'module_id' => $smcMod4->id,
                        'instructions' => "Uji pemahaman mendalam Anda mengenai Order Block institusional dan mitigasi Imbalance/FVG yang belum tersentuh.\n\n- Nilai kelulusan: 70%\n- Batas waktu: 15 menit.",
                        'passing_score' => 70,
                        'time_limit_minutes' => 15,
                        'max_attempts' => 5,
                        'shuffle_questions' => true,
                        'shuffle_options' => true,
                        'show_answers' => true,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );

                $questionsQ2 = [
                    [
                        'question' => 'Kriteria manakah yang menandai Order Block (OB) berkualitas tinggi (High Probability POI)?',
                        'type' => 'single',
                        'points' => 50,
                        'sort_order' => 1,
                        'explanation' => 'Order Block berkualitas tinggi wajib didahului oleh penyapuan likuiditas (liquidity sweep/grab), memicu pergerakan impulsif (displacement), dan meninggalkan Fair Value Gap (FVG) yang belum termitigasi.',
                        'options' => [
                            ['label' => 'Menyapu likuiditas sebelumnya, menghasilkan displacement impulsif, dan meninggalkan FVG yang valid.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Semua candle berurutan yang memiliki warna sama tanpa memperhatikan likuiditas.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Candle doji yang berada persis di tengah Bollinger Bands.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Candle dengan ukuran sumbu terpanjang pada sesi Asia.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'question' => 'Secara teknis, bagaimana Fair Value Gap (FVG) terbentuk pada grafik 3 candle?',
                        'type' => 'single',
                        'points' => 50,
                        'sort_order' => 2,
                        'explanation' => 'FVG tercipta karena ketidakseimbangan order beli/jual di mana sumbu (wick) candle pertama dan sumbu candle ketiga tidak saling bertabrakan atau menyentuh, meninggalkan celah kosong pada candle kedua.',
                        'options' => [
                            ['label' => 'Terdapat gap ketidakseimbangan di candle ke-2 di mana wick candle ke-1 dan candle ke-3 tidak saling bersentuhan.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Harga melonjak saat akhir pekan ketika pasar sedang tutup.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Ketika spread broker melonjak drastis saat pergantian hari (rollover).', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Ketika harga menyentuh support garis tren Fibonacci 0.618.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                ];

                $this->syncQuestionsAndOptions($quiz2, $questionsQ2);
            }

            // 1.C. Course Final Exam (Global Quiz, module_id = null)
            $finalQuiz = Quiz::updateOrCreate(
                [
                    'course_id' => $courseSmc->id,
                    'title' => 'Ujian Akhir Sertifikasi: Smart Money Concepts Mastery',
                ],
                [
                    'module_id' => null,
                    'instructions' => "Ujian kelulusan komprehensif SMC Mastery. Anda harus mencapai nilai minimal 80% untuk mendapatkan sertifikat kelulusan resmi platform.\n\n- Durasi: 30 Menit\n- Passing Score: 80%\n- Kesempatan Ujian: Maksimal 2x",
                    'passing_score' => 80,
                    'time_limit_minutes' => 30,
                    'max_attempts' => 2,
                    'shuffle_questions' => true,
                    'shuffle_options' => true,
                    'show_answers' => false,
                    'is_required' => true,
                    'status' => 'published',
                ]
            );

            $questionsFinal = [
                [
                    'question' => 'Dalam pendekatan Multi-Timeframe Analysis (MTF), bagaimana alur analisis SMC yang paling tepat?',
                    'type' => 'single',
                    'points' => 35,
                    'sort_order' => 1,
                    'explanation' => 'Alur MTF SMC dimulai dari Higher Timeframe (Daily/H4) untuk menentukan bias arah dan POI utama, kemudian turun ke Intermediate (H1/M15) untuk struktur internal, dan Lower Timeframe (M5/M1) untuk konfirmasi entri (CHoCH/mitigasi).',
                    'options' => [
                        ['label' => 'HTF (Daily/H4) untuk Macro Bias & POI -> LTF (M15/M1) untuk Konfirmasi CHoCH & Eksekusi Sniper.', 'is_correct' => true, 'sort_order' => 1],
                        ['label' => 'M1 untuk analisa arah besar -> Monthly untuk mencari trigger stop loss.', 'is_correct' => false, 'sort_order' => 2],
                        ['label' => 'Hanya menggunakan satu timeframe tanpa melihat timeframe di atas atau di bawahnya.', 'is_correct' => false, 'sort_order' => 3],
                        ['label' => 'Mengikuti arah candle 5 detik setiap pembukaan sesi pasar.', 'is_correct' => false, 'sort_order' => 4],
                    ],
                ],
                [
                    'question' => 'Apa yang dimaksud dengan "Liquidity Sweep" atau "Stop Hunt" dalam terminologi institusional?',
                    'type' => 'single',
                    'points' => 35,
                    'sort_order' => 2,
                    'explanation' => 'Liquidity sweep adalah pergerakan harga menembus level swing (tempat retail menaruh stop loss) hanya untuk mengisi volume order institusi sebelum harga berbalik arah secara agresif.',
                    'options' => [
                        ['label' => 'Manuver penetrasi harga melewati area stop loss retail guna mengisi volume likuiditas sebelum harga berbalik arah.', 'is_correct' => true, 'sort_order' => 1],
                        ['label' => 'Kerusakan server broker yang menyebabkan order tidak dapat dieksekusi.', 'is_correct' => false, 'sort_order' => 2],
                        ['label' => 'Penutupan posisi otomatis akibat margin call.', 'is_correct' => false, 'sort_order' => 3],
                        ['label' => 'Peningkatan spread saat rilis berita suku bunga acuan Fed.', 'is_correct' => false, 'sort_order' => 4],
                    ],
                ],
                [
                    'question' => 'Jika Anda memiliki modal $10.000 dengan toleransi risiko 1% per trade ($100), dan jarak Stop Loss sebesar 20 pips, berapa ukuran lot EURUSD yang harus Anda gunakan? (Nilai 1 pip standard lot = $10)',
                    'type' => 'single',
                    'points' => 30,
                    'sort_order' => 3,
                    'explanation' => 'Risiko per trade = $100. Stop loss = 20 pips. Nilai per pip = $100 / 20 pips = $5/pip. Karena 1 standard lot = $10/pip, maka ukuran lot = 5 / 10 = 0.50 Lot.',
                    'options' => [
                        ['label' => '0.50 Lot', 'is_correct' => true, 'sort_order' => 1],
                        ['label' => '1.00 Lot', 'is_correct' => false, 'sort_order' => 2],
                        ['label' => '0.20 Lot', 'is_correct' => false, 'sort_order' => 3],
                        ['label' => '2.00 Lot', 'is_correct' => false, 'sort_order' => 4],
                    ],
                ],
            ];

            $this->syncQuestionsAndOptions($finalQuiz, $questionsFinal);
        }

        // -------------------------------------------------------------
        // 2. Quizzes for Forex Basics Course
        // -------------------------------------------------------------
        if ($courseForex) {
            $forexMod1 = Module::where('course_id', $courseForex->id)
                ->where('title', 'like', '%Terminologi Dasar%')
                ->first();

            if ($forexMod1) {
                $quizForex1 = Quiz::updateOrCreate(
                    [
                        'course_id' => $courseForex->id,
                        'title' => 'Kuis Dasar: Kalkulasi Pip, Lot & Leverage Forex',
                    ],
                    [
                        'module_id' => $forexMod1->id,
                        'instructions' => "Kuis singkat untuk memvalidasi pemahaman Anda mengenai perhitungan pip, penggunaan lot size, dan manajemen leverage trading.\n\n- Nilai kelulusan: 70%\n- Waktu pengerjaan: 10 menit.",
                        'passing_score' => 70,
                        'time_limit_minutes' => 10,
                        'max_attempts' => 10,
                        'shuffle_questions' => false,
                        'shuffle_options' => true,
                        'show_answers' => true,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );

                $questionsForex1 = [
                    [
                        'question' => 'Jika pasangan mata uang GBP/USD bergerak dari 1.2500 ke 1.2550, berapa pips pergerakan harga tersebut?',
                        'type' => 'single',
                        'points' => 50,
                        'sort_order' => 1,
                        'explanation' => '1.2550 - 1.2500 = 0.0050. Pada pasangan mata uang 4 desimal, 1 pip adalah 0.0001, sehingga 0.0050 sama dengan 50 pips.',
                        'options' => [
                            ['label' => '50 Pips', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => '5 Pips', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => '500 Pips', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => '0.5 Pips', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                    [
                        'question' => 'Apa dampak dari penggunaan leverage yang lebih tinggi (misalnya 1:500 dibandingkan 1:50)?',
                        'type' => 'single',
                        'points' => 50,
                        'sort_order' => 2,
                        'explanation' => 'Leverage tinggi memperkecil jaminan margin yang dibutuhkan untuk membuka posisi, namun memperbesar risiko kerugian yang cepat jika tidak disertai manajemen lot dan stop loss yang ketat.',
                        'options' => [
                            ['label' => 'Menurunkan modal margin yang dibutuhkan untuk membuka posisi, namun memperbesar risiko jika tanpa kontrol lot.', 'is_correct' => true, 'sort_order' => 1],
                            ['label' => 'Membuat harga mata uang bergerak lebih cepat di chart trading.', 'is_correct' => false, 'sort_order' => 2],
                            ['label' => 'Menghilangkan kemungkinan terkena stop loss oleh broker.', 'is_correct' => false, 'sort_order' => 3],
                            ['label' => 'Mengurangi spread bid-ask menjadi nol secara permanen.', 'is_correct' => false, 'sort_order' => 4],
                        ],
                    ],
                ];

                $this->syncQuestionsAndOptions($quizForex1, $questionsForex1);
            }
        }
    }

    /**
     * Helper to sync questions and their options cleanly.
     */
    protected function syncQuestionsAndOptions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $qData) {
            $question = QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question' => $qData['question'],
                ],
                [
                    'type' => $qData['type'],
                    'points' => $qData['points'],
                    'sort_order' => $qData['sort_order'],
                    'explanation' => $qData['explanation'] ?? null,
                ]
            );

            foreach ($qData['options'] as $optData) {
                QuizOption::updateOrCreate(
                    [
                        'question_id' => $question->id,
                        'label' => $optData['label'],
                    ],
                    [
                        'is_correct' => $optData['is_correct'],
                        'sort_order' => $optData['sort_order'],
                    ]
                );
            }
        }
    }
}
