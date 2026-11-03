<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\Module;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Learning\Models\QuizOption;
use App\Domain\Learning\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Kursus Forex lengkap (Forex Trading Basics for Beginners).
 * Owns the modules, lessons, and quizzes of that course. Materi berbentuk teks
 * (type = article) supaya tampil rapi di classroom tanpa bergantung pada video.
 */
class ForexCourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'forex-trading-basics-beginners')->first();

        if (! $course) {
            return;
        }

        $curriculum = $this->curriculum();

        $totalSeconds = 0;
        $lessonCount = 0;

        foreach ($curriculum as $moduleOrder => $module) {
            $moduleModel = Module::updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $moduleOrder + 1],
                [
                    'title' => $module['title'],
                    'description' => $module['description'],
                    'is_published' => true,
                ]
            );

            foreach ($module['lessons'] as $lessonOrder => $lesson) {
                Lesson::updateOrCreate(
                    ['module_id' => $moduleModel->id, 'sort_order' => $lessonOrder + 1],
                    [
                        'uuid' => (string) Str::uuid(),
                        'course_id' => $course->id,
                        'title' => $lesson['title'],
                        'slug' => Str::slug($lesson['title']),
                        'type' => 'article',
                        'content' => $lesson['content'],
                        'duration_seconds' => $lesson['minutes'] * 60,
                        'is_preview' => $moduleOrder === 0 && $lessonOrder === 0,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );

                $totalSeconds += $lesson['minutes'] * 60;
                $lessonCount++;
            }

            $this->seedQuiz($course, $moduleModel, $module['quiz']);
        }

        $course->update([
            'title' => 'Forex Trading Basics for Beginners',
            'subtitle' => 'Dari nol sampai siap praktik: pasangan mata uang, candlestick, pip & lot, manajemen risiko, analisa dasar, dan psikologi trading.',
            'description' => "Kursus Forex lengkap untuk pemula yang ingin trading secara terarah, bukan asal tebak.\n\nAnda akan mempelajari cara kerja pasar valuta asing, membaca candlestick dan struktur harga, menghitung pip dan lot dengan benar, menerapkan aturan risiko 1% per trade, serta membangun kebiasaan jurnal dan disiplin emosi.\n\nSetiap bab dilengkapi kuis singkat. Lulus semua kuis dengan skor minimal 70% untuk mendapatkan sertifikat.",
            'level' => 'beginner',
            'duration_seconds' => $totalSeconds,
            'lessons_count' => $lessonCount,
            'status' => 'published',
        ]);
    }

    protected function seedQuiz(Course $course, Module $module, array $quiz): void
    {
        $quizModel = Quiz::updateOrCreate(
            ['course_id' => $course->id, 'module_id' => $module->id, 'title' => $quiz['title']],
            [
                'instructions' => $quiz['instructions'],
                'passing_score' => 70,
                'time_limit_minutes' => 10,
                'max_attempts' => 5,
                'shuffle_questions' => false,
                'shuffle_options' => true,
                'show_answers' => true,
                'is_required' => true,
                'status' => 'published',
            ]
        );

        foreach ($quiz['questions'] as $index => $q) {
            $question = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quizModel->id, 'question' => $q['question']],
                [
                    'type' => 'single',
                    'points' => intdiv(100, max(1, count($quiz['questions']))),
                    'sort_order' => $index + 1,
                    'explanation' => $q['explanation'],
                ]
            );

            foreach ($q['options'] as $optIndex => $label) {
                QuizOption::updateOrCreate(
                    ['question_id' => $question->id, 'label' => $label],
                    [
                        'is_correct' => $optIndex === $q['answer'],
                        'sort_order' => $optIndex + 1,
                    ]
                );
            }
        }
    }

    /**
     * Kurikulum: 6 bab, masing-masing 3 pelajaran dan 1 kuis.
     */
    protected function curriculum(): array
    {
        return [
            [
                'title' => 'Bab 1: Mengenal Pasar Forex',
                'description' => 'Apa itu forex, siapa pelakunya, pasangan mata uang, dan jam sesi pasar.',
                'lessons' => [
                    [
                        'title' => 'Apa Itu Forex dan Bagaimana Pasar Bekerja',
                        'minutes' => 12,
                        'content' => "Forex (foreign exchange) adalah pasar tempat mata uang dijual dan dibeli satu sama lain. Pasar ini adalah pasar keuangan terbesar di dunia dengan perputaran harian yang sangat besar, dan buka hampir 24 jam dari Senin sampai Jumat.\n\nPelaku di pasar forex beragam: bank sentral, bank komersial, perusahaan multinasional yang melakukan transaksi valuta asing, hedge fund, sampai trader ritel seperti Anda.\n\nDi pasar ritel, trader tidak membeli mata uang fisik. Anda membuka posisi kontrak selisih (CFD atau kontrak berjangka) melalui broker. Untungnya datang dari selisih harga saat posisi ditutup, bukan dari kepemilikan mata uang itu sendiri.\n\nPoin penting: harga selalu dinyatakan sebagai pasangan. Naik atau turunnya harga selalu berarti satu mata uang menguat terhadap mata uang lainnya.",
                    ],
                    [
                        'title' => 'Pasangan Mata Uang: Major, Minor, dan Exotic',
                        'minutes' => 14,
                        'content' => "Setiap transaksi forex selalu melibatkan dua mata uang. Mata uang pertama disebut base currency, dan mata uang kedua disebut quote currency. Pada EUR/USD, EUR adalah base dan USD adalah quote. Jika EUR/USD bernilai 1.0850, artinya satu euro dihargai 1,0850 dolar AS.\n\nPasangan dibagi menjadi tiga kelompok:\n\n- Major: pasangan yang melibatkan USD dengan mata uang utama dunia, misalnya EUR/USD, GBP/USD, USD/JPY, USD/CHF, AUD/USD, USD/CAD, dan NZD/USD. Spread-nya biasanya paling rendah dan likuiditasnya paling tinggi.\n- Minor (cross): pasangan mata uang utama tanpa USD, misalnya EUR/GBP atau EUR/JPY. Likuiditasnya masih baik, tetapi spread biasanya lebih lebar.\n- Exotic: pasangan dengan mata uang negara berkembang, misalnya USD/TRY atau USD/ZAR. Spread lebar dan pergerakannya bisa sangat liar.\n\nPemula sebaiknya fokus pada pasangan major dulu. Selain spread yang lebih kecil, data dan analisanya juga lebih banyak tersedia.",
                    ],
                    [
                        'title' => 'Sesi Pasar, Jam Trading, dan Volatilitas',
                        'minutes' => 13,
                        'content' => "Pasar forex dibagi menjadi empat sesi besar: Sydney, Tokyo, London, dan New York. Jam berikut adalah perkiraan dalam WIB dan dapat bergeser mengikuti jam musim panas atau dingin:\n\n- Sesi Sydney: sekitar 05.00 sampai 14.00 WIB\n- Sesi Tokyo: sekitar 07.00 sampai 16.00 WIB\n- Sesi London: sekitar 15.00 sampai 00.00 WIB\n- Sesi New York: sekitar 20.00 sampai 05.00 WIB\n\nVolatilitas paling tinggi biasanya terjadi saat sesi London dan New York saling tumpang tindih, sekitar pukul 20.00 sampai 00.00 WIB. Pada jam ini likuiditas besar dan pergerakan harga sering lebih tajam.\n\nSebaliknya, saat sesi Asia berjalan sendiri, pergerakan pasangan seperti EUR/USD sering lebih sepi dan sempit. Pemula bisa memakai jam ini untuk belajar membaca chart tanpa tekanan.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 1: Mengenal Pasar Forex',
                    'instructions' => "Kuis singkat tentang dasar pasar forex, pasangan mata uang, dan sesi trading.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'Pada pasangan EUR/USD, mata uang mana yang disebut base currency?',
                            'options' => ['EUR', 'USD', 'Keduanya sama', 'Tergantung broker'],
                            'answer' => 0,
                            'explanation' => 'Base currency adalah mata uang pertama dalam pasangan. Pada EUR/USD, base-nya adalah EUR.',
                        ],
                        [
                            'question' => 'Manakah yang termasuk pasangan major?',
                            'options' => ['EUR/USD', 'EUR/GBP', 'USD/TRY', 'EUR/JPY'],
                            'answer' => 0,
                            'explanation' => 'Major selalu melibatkan USD dengan mata uang utama dunia. EUR/GBP dan EUR/JPY adalah minor, sedangkan USD/TRY adalah exotic.',
                        ],
                        [
                            'question' => 'Kapan volatilitas biasanya paling tinggi di pasar forex?',
                            'options' => ['Saat sesi Sydney sendirian', 'Saat sesi London dan New York tumpang tindih', 'Saat akhir pekan', 'Saat libur nasional Indonesia'],
                            'answer' => 1,
                            'explanation' => 'Overlap London dan New York mempertemukan likuiditas terbesar, sehingga pergerakan harga sering paling tajam.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Bab 2: Membaca Candlestick dan Struktur Harga',
                'description' => 'Anatomi candlestick, tren, dan cara membaca pergerakan harga dari chart.',
                'lessons' => [
                    [
                        'title' => 'Anatomi Candlestick: Body dan Wick',
                        'minutes' => 15,
                        'content' => "Setiap candlestick merangkum pergerakan harga dalam satu periode waktu, misalnya 1 menit, 1 jam, atau 1 hari. Candlestick punya empat nilai: open, high, low, dan close.\n\nBagian-bagiannya:\n\n- Body: area antara open dan close. Body besar berarti tekanan beli atau jual sedang kuat.\n- Upper wick (sumbu atas): harga tertinggi yang sempat dicapai di atas body.\n- Lower wick (sumbu bawah): harga terendah yang sempat dicapai di bawah body.\n\nCandle bullish (hijau atau putih) menutup di atas harga buka. Candle bearish (merah atau hitam) menutup di bawah harga buka. Warna bisa diatur sesuai selera, tetapi maknanya tetap sama.\n\nWick panjang di atas sering menandakan penolakan harga naik. Wick panjang di bawah sering menandakan penolakan harga turun. Dari sinilah pola-pola reversal dibaca.",
                    ],
                    [
                        'title' => 'Tren: Higher High, Higher Low, dan Lower Low',
                        'minutes' => 16,
                        'content' => "Tren adalah arah umum pergerakan harga. Tiga kondisi dasar yang perlu dikenali:\n\n- Uptrend: harga membentuk higher high (puncak lebih tinggi) dan higher low (lembah lebih tinggi).\n- Downtrend: harga membentuk lower high (puncak lebih rendah) dan lower low (lembah lebih rendah).\n- Sideways (range): harga bergerak di antara dua batas tanpa membentuk pola high dan low yang jelas.\n\nAturan praktisnya: trade searah tren lebih mudah dikelola daripada melawan tren. Saat uptrend, cari peluang beli setelah harga mundur ke area support. Saat downtrend, cari peluang jual setelah harga naik ke area resistance.\n\nTren bisa berubah. Patahan tren pertama biasanya ditandai harga gagal membuat high baru di uptrend, atau gagal membuat low baru di downtrend.",
                    ],
                    [
                        'title' => 'Support dan Resistance',
                        'minutes' => 17,
                        'content' => "Support adalah area harga yang sering memantulkan pergerakan turun. Resistance adalah area harga yang sering menahan pergerakan naik. Keduanya terbentuk dari perilaku pelaku pasar yang mengingat level harga tertentu.\n\nCara menandai level yang kuat:\n\n- Level yang sudah beberapa kali disentuh dan memantul.\n- Area swing high atau swing low yang jelas di chart H4 atau Daily.\n- Level bulat seperti 1.1000 atau 150.00 pada pasangan JPY, karena banyak order menumpuk di sana.\n\nSaat resistance ditembus dengan kuat dan harga menutup di atasnya, level itu bisa berubah peran menjadi support. Perubahan peran ini disebut role reversal dan sering dipakai sebagai titik entry.\n\nJangan menggambar terlalu banyak garis. Chart yang penuh garis justru membingungkan. Pilih dua sampai tiga level terkuat di timeframe yang Anda pakai.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 2: Candlestick dan Struktur Harga',
                    'instructions' => "Kuis tentang anatomi candlestick, tren, dan support resistance.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'Candle bullish ditandai dengan kondisi apa?',
                            'options' => ['Close lebih rendah dari open', 'Close lebih tinggi dari open', 'High sama dengan low', 'Tidak ada wick'],
                            'answer' => 1,
                            'explanation' => 'Candle bullish menutup di atas harga buka. Warna hanya penanda visual, yang penting adalah posisi close terhadap open.',
                        ],
                        [
                            'question' => 'Pola harga apa yang menandakan uptrend?',
                            'options' => ['Lower high dan lower low', 'Higher high dan higher low', 'Harga datar tanpa puncak', 'Semua candle berukuran sama'],
                            'answer' => 1,
                            'explanation' => 'Uptrend ditandai higher high dan higher low secara berurutan.',
                        ],
                        [
                            'question' => 'Apa arti wick atas yang panjang pada sebuah candle?',
                            'options' => ['Harga sempat naik tinggi lalu ditolak turun', 'Harga pasti akan naik terus', 'Spread broker sedang besar', 'Pasar sedang libur'],
                            'answer' => 0,
                            'explanation' => 'Wick atas panjang menandakan harga sempat naik ke atas tetapi ditolak, sehingga penutupannya jauh di bawah high.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Bab 3: Pip, Lot, Margin, dan Leverage',
                'description' => 'Cara menghitung pip, nilai per pip, ukuran lot, dan penggunaan leverage yang aman.',
                'lessons' => [
                    [
                        'title' => 'Pip dan Pipette: Satuan Pergerakan Harga',
                        'minutes' => 13,
                        'content' => "Pip (percentage in point) adalah satuan standar pergerakan harga forex. Untuk sebagian besar pasangan, 1 pip bernilai 0,0001. Contohnya, jika GBP/USD bergerak dari 1,2500 ke 1,2550, selisihnya adalah 50 pip.\n\nPasangan yang melibatkan JPY menggunakan dua angka desimal, sehingga 1 pip bernilai 0,01. Contohnya, USD/JPY dari 150,00 ke 150,50 berarti 50 pip.\n\nBeberapa broker juga menampilkan pipette, yaitu pecahan satu pip (biasanya 0,00001 atau 0,001 pada JPY). Pipette berguna untuk ketepatan harga, tetapi untuk perencanaan risiko Anda cukup menghitung dalam pip.\n\nLatihan: hitung selisih pip dari entry 1,0850 ke take profit 1,0920 pada EUR/USD. Jawabannya 70 pip.",
                    ],
                    [
                        'title' => 'Menghitung Nilai Pip dan Ukuran Lot',
                        'minutes' => 18,
                        'content' => "Ukuran lot menentukan berapa besar nilai setiap pergerakan pip. Standard lot = 100.000 unit mata uang dasar. Mini lot = 10.000 unit. Micro lot = 1.000 unit.\n\nPada pasangan dengan USD sebagai quote (misalnya EUR/USD), nilai 1 pip untuk 1 standard lot kira-kira 10 dolar AS. Mini lot bernilai 1 dolar per pip, dan micro lot bernilai 0,10 dolar per pip.\n\nRumus dasar penentuan lot:\n\nLot = Dana risiko (dalam USD) ÷ (Jarak stop loss dalam pip × Nilai pip per 1 lot)\n\nContoh: saldo 10.000 dolar, risiko 1% berarti 100 dolar. Stop loss 20 pip, nilai pip per 1 lot 10 dolar. Maka lot = 100 ÷ (20 × 10) = 0,5 lot.\n\nJika broker Anda memakai mata uang rekening lain (misalnya rupiah), konversikan dulu nilai risiko ke mata uang yang sama.",
                    ],
                    [
                        'title' => 'Margin dan Leverage dengan Bijak',
                        'minutes' => 14,
                        'content' => "Leverage memungkinkan Anda membuka posisi yang nilainya jauh lebih besar dari saldo. Leverage 1:100 berarti margin yang dibutuhkan kira-kira 1% dari nilai posisi. Untuk posisi senilai 100.000 dolar, margin yang dikunci sekitar 1.000 dolar.\n\nLeverage memperbesar keuntungan sekaligus kerugian. Yang berbahaya adalah leverage tinggi dipakai dengan ukuran lot yang juga besar. Akibatnya, pergerakan kecil bisa menguras akun.\n\nMargin level adalah rasio antara ekuitas dan margin yang terpakai. Jika angka ini turun terlalu rendah, broker bisa menutup posisi secara otomatis (margin call atau stop out).\n\nPrinsip aman untuk pemula:\n\n- Leverage tidak selalu harus tinggi. Yang penting adalah ukuran lot yang sesuai dengan risiko.\n- Jangan memakai lebih dari sebagian kecil saldo sebagai margin.\n- Selalu pasang stop loss sebelum posisi dibuka.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 3: Pip, Lot, dan Leverage',
                    'instructions' => "Kuis tentang perhitungan pip, nilai pip, lot, dan leverage.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'GBP/USD bergerak dari 1,2500 ke 1,2550. Berapa pip pergerakannya?',
                            'options' => ['5 pip', '50 pip', '500 pip', '0,5 pip'],
                            'answer' => 1,
                            'explanation' => 'Selisihnya 0,0050. Karena 1 pip = 0,0001, maka 0,0050 sama dengan 50 pip.',
                        ],
                        [
                            'question' => 'Berapa nilai kira-kira per pip untuk 1 standard lot EUR/USD?',
                            'options' => ['1 dolar', '10 dolar', '100 dolar', '0,10 dolar'],
                            'answer' => 1,
                            'explanation' => 'Pada pasangan dengan USD sebagai quote, 1 standard lot (100.000 unit) bernilai kira-kira 10 dolar per pip.',
                        ],
                        [
                            'question' => 'Risiko Anda 100 dolar, stop loss 20 pip, dan nilai pip 10 dolar per lot. Berapa ukuran lot yang tepat?',
                            'options' => ['0,1 lot', '0,5 lot', '2 lot', '5 lot'],
                            'answer' => 1,
                            'explanation' => 'Lot = 100 ÷ (20 × 10) = 0,5 lot.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Bab 4: Manajemen Risiko 1% dan Risk Reward',
                'description' => 'Aturan risiko per trade, rasio risk reward, dan perhitungan win rate minimum.',
                'lessons' => [
                    [
                        'title' => 'Aturan Risiko 1% per Trade',
                        'minutes' => 15,
                        'content' => "Aturan paling penting dalam trading adalah menjaga agar satu kesalahan tidak menghancurkan akun. Aturan 1% berarti risiko maksimal per trade adalah 1% dari saldo. Sebagian trader memakai 2% jika pengalamannya sudah cukup.\n\nContoh: saldo 10.000 dolar. Risiko maksimal per trade adalah 100 dolar. Jika stop loss terkena, akun turun 1%, bukan 20% atau lebih.\n\nDengan aturan ini, Anda perlu kehilangan 10 trade berturut-turut untuk turun sekitar 10%. Ini jauh lebih mudah dikelola dibanding satu trade besar yang merusak mental dan modal.\n\nAturan ini juga memisahkan keputusan entry dari hasil. Anda tetap bisa trading dengan tenang karena kerugian sudah ditentukan sejak awal.",
                    ],
                    [
                        'title' => 'Risk Reward Ratio dan Win Rate Minimum',
                        'minutes' => 16,
                        'content' => "Risk reward ratio (RR) membandingkan jarak stop loss dengan jarak take profit. RR 1:2 berarti Anda berisiko 1 unit untuk mendapatkan 2 unit.\n\nRR membantu menentukan berapa win rate minimum yang dibutuhkan agar impas. Rumusnya:\n\nWin rate impas = 1 ÷ (1 + RR)\n\nUntuk RR 1:2, win rate impas = 1 ÷ 3 = sekitar 33%. Artinya, dengan RR 1:2 Anda masih bisa untung walaupun hanya menang 1 dari 3 trade.\n\nJika RR 1:1, Anda butuh win rate di atas 50%. Jika RR 1:3, win rate impas turun ke 25%.\n\nPoin penting: RR tinggi saja tidak cukup. Target take profit harus realistis dan didukung struktur harga, misalnya level resistance berikutnya.",
                    ],
                    [
                        'title' => 'Menyusun Rencana Trade Lengkap',
                        'minutes' => 17,
                        'content' => "Setiap trade sebaiknya punya rencana tertulis sebelum posisi dibuka. Rencana minimal berisi:\n\n- Pasangan dan timeframe yang dipakai.\n- Alasan entry (misalnya pantulan dari support pada tren naik).\n- Level entry, stop loss, dan take profit.\n- Ukuran lot yang sudah dihitung dengan aturan 1%.\n- Kondisi yang membatalkan rencana.\n\nContoh rencana: beli EUR/USD di 1,0850, stop loss di 1,0830 (20 pip), take profit di 1,0890 (40 pip). Saldo 10.000 dolar, risiko 1% atau 100 dolar, nilai pip 1 lot 10 dolar. Lot = 100 ÷ (20 × 10) = 0,5 lot. RR = 1:2.\n\nSetelah posisi dibuka, jangan memperlebar stop loss. Memindahkan stop loss menjauh dari entry adalah kebiasaan yang paling sering menghancurkan akun pemula.\n\nTrailing stop atau break even boleh dipakai saat harga sudah bergerak sesuai rencana, tetapi aturannya harus ditentukan sebelumnya.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 4: Manajemen Risiko',
                    'instructions' => "Kuis tentang aturan 1%, risk reward, dan rencana trade.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'Saldo Anda 10.000 dolar dan aturan risiko 1% per trade. Berapa risiko maksimal per trade?',
                            'options' => ['10 dolar', '100 dolar', '1.000 dolar', '500 dolar'],
                            'answer' => 1,
                            'explanation' => '1% dari 10.000 dolar adalah 100 dolar.',
                        ],
                        [
                            'question' => 'Dengan risk reward 1:2, berapa win rate minimum agar impas?',
                            'options' => ['50%', '33%', '66%', '20%'],
                            'answer' => 1,
                            'explanation' => 'Win rate impas = 1 ÷ (1 + 2) = sekitar 33%.',
                        ],
                        [
                            'question' => 'Setelah posisi dibuka, harga bergerak melawan Anda mendekati stop loss. Apa tindakan yang paling tepat?',
                            'options' => ['Menjauhkan stop loss agar tidak kena', 'Menambah lot untuk menutup kerugian', 'Membiarkan rencana awal berjalan', 'Menutup akun'],
                            'answer' => 2,
                            'explanation' => 'Rencana yang sudah disusun harus dijalankan. Memperlebar stop loss atau menambah lot adalah kebiasaan yang merusak akun.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Bab 5: Analisa Teknikal Dasar',
                'description' => 'Moving average, RSI, dan cara menggabungkan indikator dengan struktur harga.',
                'lessons' => [
                    [
                        'title' => 'Moving Average untuk Membaca Tren',
                        'minutes' => 15,
                        'content' => "Moving average (MA) menghitung rata-rata harga penutupan dalam jumlah periode tertentu. Fungsinya untuk menghaluskan noise dan melihat arah tren.\n\nDua jenis yang umum: Simple Moving Average (SMA) dan Exponential Moving Average (EMA). EMA memberi bobot lebih besar pada harga terbaru, sehingga lebih cepat merespons perubahan.\n\nBeberapa pemakaian dasar:\n\n- Harga di atas MA 200 cenderung menandakan tren naik jangka panjang, dan di bawahnya menandakan tren turun.\n- Persilangan MA cepat (misalnya EMA 20) di atas MA lambat (misalnya EMA 50) sering dibaca sebagai sinyal awal tren naik.\n\nMA adalah indikator yang tertinggal (lagging). Ia membantu konfirmasi, bukan meramal. Selalu gabungkan dengan struktur harga.",
                    ],
                    [
                        'title' => 'RSI: Overbought, Oversold, dan Divergence',
                        'minutes' => 16,
                        'content' => "Relative Strength Index (RSI) adalah osilator yang bergerak antara 0 dan 100. Nilai di atas 70 sering dibaca sebagai kondisi overbought (harga mungkin sudah terlalu naik). Nilai di bawah 30 sering dibaca sebagai oversold (harga mungkin sudah terlalu turun).\n\nHati-hati: overbought tidak otomatis berarti harga akan turun. Di tren yang kuat, RSI bisa bertahan di atas 70 cukup lama.\n\nDivergence adalah kondisi ketika harga membuat puncak baru tetapi RSI tidak. Divergence bearish bisa menjadi tanda tenaga naik mulai melemah. Divergence bullish terjadi sebaliknya.\n\nGunakan RSI sebagai pelengkap. Keputusan utama tetap berasal dari struktur harga dan level support atau resistance.",
                    ],
                    [
                        'title' => 'Menggabungkan Indikator dengan Struktur Harga',
                        'minutes' => 18,
                        'content' => "Kesalahan umum pemula adalah memasang terlalu banyak indikator sampai chart penuh. Pendekatan yang lebih sehat adalah memakai sedikit alat dengan fungsi yang jelas:\n\n- Struktur harga: tentukan tren (higher high dan higher low atau sebaliknya).\n- Level penting: tandai support dan resistance terkuat.\n- Konfirmasi: gunakan MA untuk arah tren dan RSI untuk kondisi momentum.\n\nContoh alur analisa: di uptrend H4, harga mundur ke support dan membentuk candle pantulan dengan wick bawah panjang. EMA 20 berada di atas EMA 50, dan RSI turun ke area 40-50 lalu naik lagi. Ini menjadi kandidat entry beli dengan stop loss di bawah support.\n\nSebelum menggunakan setup, tuliskan syarat-syaratnya. Setup yang tidak memenuhi semua syarat dilewati saja.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 5: Analisa Teknikal Dasar',
                    'instructions' => "Kuis tentang moving average, RSI, dan penggunaan indikator.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'Apa fungsi utama moving average dalam analisa teknikal?',
                            'options' => ['Meramal harga pasti di masa depan', 'Menghaluskan harga dan membantu membaca arah tren', 'Mengukur spread broker', 'Menghitung ukuran lot'],
                            'answer' => 1,
                            'explanation' => 'Moving average membantu melihat arah tren dengan menghaluskan fluktuasi harga. Ia bersifat konfirmasi, bukan ramalan.',
                        ],
                        [
                            'question' => 'RSI di atas 70 sering dibaca sebagai kondisi apa?',
                            'options' => ['Oversold', 'Overbought', 'Sideways murni', 'Libur pasar'],
                            'answer' => 1,
                            'explanation' => 'RSI di atas 70 sering dibaca sebagai overbought, yaitu harga mungkin sudah terlalu naik. Ini bukan jaminan pembalikan arah.',
                        ],
                        [
                            'question' => 'Mengapa sebaiknya tidak memasang terlalu banyak indikator?',
                            'options' => ['Karena indikator dilarang broker', 'Karena chart jadi membingungkan dan sinyalnya saling bertabrakan', 'Karena indikator selalu salah', 'Karena indikator hanya untuk pro'],
                            'answer' => 1,
                            'explanation' => 'Terlalu banyak indikator membuat analisa rumit dan sering menghasilkan sinyal yang bertentangan. Pilih beberapa alat dengan fungsi jelas.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Bab 6: Psikologi Trading dan Jurnal',
                'description' => 'Mengelola emosi, menghindari overtrading, dan membangun kebiasaan jurnal.',
                'lessons' => [
                    [
                        'title' => 'Mengendalikan Emosi: Takut, Serakah, dan Balas Dendam',
                        'minutes' => 15,
                        'content' => "Psikologi sering lebih menentukan hasil trading daripada analisa. Tiga emosi yang paling sering merusak:\n\n- Takut: menutup posisi terlalu cepat padahal rencana belum selesai.\n- Serakah: menahan posisi melewati target atau menambah lot tanpa aturan.\n- Balas dendam: langsung membuka trade baru setelah rugi untuk menutup kerugian.\n\nCara mengatasinya bukan dengan menjadi tanpa emosi, tetapi dengan membuat aturan sebelum masuk pasar. Rencana trade yang tertulis adalah pagar pengaman. Saat emosi naik, Anda cukup mengikuti rencana.\n\nJika sedang emosional, berhenti sejenak adalah keputusan yang sah. Tidak ada kewajiban untuk selalu trading setiap hari.",
                    ],
                    [
                        'title' => 'Overtrading dan Aturan Stop Harian',
                        'minutes' => 14,
                        'content' => "Overtrading adalah membuka terlalu banyak posisi, sering kali tanpa setup yang jelas. Gejalanya: masuk pasar karena bosan, mengejar harga yang sudah bergerak jauh, atau membuka lagi trade setelah rugi.\n\nUntuk mencegahnya, tetapkan aturan harian:\n\n- Batas maksimal jumlah trade per hari, misalnya 2 atau 3.\n- Batas kerugian harian, misalnya 3% dari saldo. Jika tercapai, berhenti trading hari itu.\n- Batas kerugian beruntun, misalnya 3 trade rugi berturut-turut, lalu berhenti dan evaluasi.\n\nAturan ini terdengar sederhana, tetapi justru yang paling sulit dijalankan saat emosi sedang tinggi. Karena itu, tulis aturannya dan tempel di tempat yang terlihat saat trading.",
                    ],
                    [
                        'title' => 'Jurnal Trading: Alat Evaluasi Paling Jujur',
                        'minutes' => 16,
                        'content' => "Jurnal trading adalah catatan setiap transaksi beserta alasannya. Jurnal membantu Anda melihat pola yang tidak terlihat dari ingatan saja.\n\nKolom minimal yang perlu dicatat:\n\n- Tanggal, pasangan, arah (buy atau sell), dan timeframe.\n- Alasan entry dan kondisi pasar saat itu.\n- Entry, stop loss, take profit, lot, dan risiko dalam persen.\n- Hasil dalam pip dan dalam dolar, serta R (hasil dibagi risiko awal).\n- Catatan emosi: apakah Anda mengikuti rencana atau tidak.\n\nSetiap akhir minggu, hitung win rate, rata-rata RR, dan jumlah trade yang melanggar rencana. Dari sini, perbaikan biasanya terlihat jelas.\n\nJurnal yang jujur lebih berharga daripada jurnal yang rapi. Catat juga trade yang Anda tidak ambil, karena itu juga bagian dari disiplin.",
                    ],
                ],
                'quiz' => [
                    'title' => 'Kuis Bab 6: Psikologi dan Jurnal',
                    'instructions' => "Kuis tentang emosi trading, overtrading, dan jurnal.\n\n- Nilai kelulusan: 70%\n- Waktu: 10 menit",
                    'questions' => [
                        [
                            'question' => 'Setelah mengalami kerugian, apa respons yang paling sehat?',
                            'options' => ['Langsung membuka trade baru untuk menutup rugi', 'Menggandakan lot', 'Berhenti sejenak dan mengevaluasi sesuai aturan', 'Mengabaikan stop loss'],
                            'answer' => 2,
                            'explanation' => 'Membalas dendam di pasar hampir selalu menambah kerugian. Berhenti dan mengevaluasi adalah respons yang sehat.',
                        ],
                        [
                            'question' => 'Apa yang dimaksud overtrading?',
                            'options' => ['Membuka posisi dengan setup yang jelas saja', 'Membuka terlalu banyak posisi, sering tanpa setup yang jelas', 'Memakai stop loss yang rapat', 'Trading hanya di sesi London'],
                            'answer' => 1,
                            'explanation' => 'Overtrading adalah trading berlebihan, biasanya didorong bosan, emosi, atau keinginan mengejar kerugian.',
                        ],
                        [
                            'question' => 'Apa manfaat utama jurnal trading?',
                            'options' => ['Menjadi bukti untuk pamer ke media sosial', 'Melihat pola kesalahan dan performa secara objektif', 'Menghilangkan kebutuhan stop loss', 'Menjamin profit setiap bulan'],
                            'answer' => 1,
                            'explanation' => 'Jurnal membantu mengevaluasi performa dan pola perilaku secara objektif, sehingga perbaikan bisa dilakukan dengan terarah.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
