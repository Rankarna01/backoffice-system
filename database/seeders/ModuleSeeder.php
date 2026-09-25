<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds for Course Modules.
     */
    public function run(): void
    {
        $courseSmc = Course::where('slug', 'smart-money-concepts-mastery')->first();
        $courseForex = Course::where('slug', 'forex-trading-basics-beginners')->first();
        $courseGold = Course::where('slug', 'gold-xauusd-trading-playbook')->first();
        $courseCrypto = Course::where('slug', 'crypto-derivatives-order-flow')->first();

        // 1. Modules for SMC Course
        if ($courseSmc) {
            $smcModules = [
                [
                    'title' => 'Bab 1: Fondasi & Logika Pasar Smart Money',
                    'description' => 'Memahami institusi vs retail, mekanisme likuiditas, dan siklus akumulasi distribusi.',
                    'document_file' => 'modules/documents/panduan-smc-liquidity.pdf',
                    'sort_order' => 1,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 2: Market Structure, BOS, dan CHoCH',
                    'description' => 'Membaca Break of Structure (BOS), Change of Character (CHoCH), dan validasi swing high/low.',
                    'document_file' => 'modules/documents/market-structure-presentation.pptx',
                    'sort_order' => 2,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 3: Liquidity Pools & Inducement',
                    'description' => 'Mendeteksi rekayasa likuiditas: Equal Highs/Lows, Buy-Side Liquidity (BSL), dan Sell-Side Liquidity (SSL).',
                    'sort_order' => 3,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 4: Supply & Demand, Order Block, dan FVG',
                    'description' => 'Identifikasi Order Block yang belum ter-mitigasi, Imbalance, dan Fair Value Gap (FVG).',
                    'sort_order' => 4,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 5: Multi-Timeframe Analysis & Sniper Entry Execution',
                    'description' => 'Sinkronisasi Daily/H4 bias ke M15/M5/M1 entry trigger dengan konfirmasi mitigasi.',
                    'sort_order' => 5,
                    'is_published' => true,
                ],
            ];

            foreach ($smcModules as $mod) {
                Module::updateOrCreate(
                    ['course_id' => $courseSmc->id, 'title' => $mod['title']],
                    $mod
                );
            }
        }

        // 2. Modules for Forex Course
        if ($courseForex) {
            $forexModules = [
                [
                    'title' => 'Bab 1: Pengenalan Pasar Forex & Karakteristik Pasangan Mata Uang',
                    'description' => 'Memahami pairs major, minor, dan exotic serta karakteristik sesi London dan New York.',
                    'sort_order' => 1,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 2: Kalkulasi Pip, Lot, Margin, dan Leverage',
                    'description' => 'Rumus perhitungan lot size dan penggunaan leverage yang bijak agar modal terlindungi.',
                    'document_file' => 'modules/documents/forex-lot-calculator-journal.xlsx',
                    'sort_order' => 2,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 3: Rule Manajemen Resiko 1% & Risk-Reward Ratio',
                    'description' => 'Membangun disiplin stop loss dan target profit minimum 1:2 untuk pertumbuhan portofolio.',
                    'sort_order' => 3,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 4: Psikologi Trading & Jurnal Evaluasi',
                    'description' => 'Mengontrol emosi trading, menghindari overtrading, dan membuat rutinitas jurnal berkala.',
                    'sort_order' => 4,
                    'is_published' => true,
                ],
            ];

            foreach ($forexModules as $mod) {
                Module::updateOrCreate(
                    ['course_id' => $courseForex->id, 'title' => $mod['title']],
                    $mod
                );
            }
        }

        // 3. Modules for Gold Course
        if ($courseGold) {
            $goldModules = [
                [
                    'title' => 'Bab 1: Karakteristik Volatilitas & Jam Trading Emas',
                    'description' => 'Memahami pergerakan XAUUSD pada sesi London Fix dan pembukaan bursa komoditas AS.',
                    'sort_order' => 1,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 2: Pengaruh DXY, Yield US10Y, dan Data Inflasi CPI',
                    'description' => 'Menganalisis sentimen makro ekonomi yang menggerakkan tren harga emas dunia.',
                    'sort_order' => 2,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 3: Setup Breakout & Scalping XAUUSD',
                    'description' => 'Strategi eksekusi intraday momentum dengan stop loss ketat pada time frame M5-M15.',
                    'sort_order' => 3,
                    'is_published' => true,
                ],
            ];

            foreach ($goldModules as $mod) {
                Module::updateOrCreate(
                    ['course_id' => $courseGold->id, 'title' => $mod['title']],
                    $mod
                );
            }
        }

        // 4. Modules for Crypto Course
        if ($courseCrypto) {
            $cryptoModules = [
                [
                    'title' => 'Bab 1: Pengenalan Pasar Derivatif Kripto & Perpetual Swaps',
                    'description' => 'Mekanisme kontrak perpetual, funding rate, dan likuidasi bertingkat di exchange kripto.',
                    'sort_order' => 1,
                    'is_published' => true,
                ],
                [
                    'title' => 'Bab 2: Analisa Order Book, Delta Volume, dan Open Interest',
                    'description' => 'Mendeteksi pergerakan whale dan perangkap likuidasi jangka pendek.',
                    'sort_order' => 2,
                    'is_published' => false,
                ],
            ];

            foreach ($cryptoModules as $mod) {
                Module::updateOrCreate(
                    ['course_id' => $courseCrypto->id, 'title' => $mod['title']],
                    $mod
                );
            }
        }
    }
}
