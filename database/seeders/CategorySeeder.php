<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds for Course Categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'type' => 'course',
                'slug' => 'price-action-smc',
                'name' => 'Price Action & Smart Money Concepts',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'type' => 'course',
                'slug' => 'forex-mastery',
                'name' => 'Forex Market Mastery',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'type' => 'course',
                'slug' => 'gold-commodities',
                'name' => 'Gold & Commodities Strategy',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'type' => 'course',
                'slug' => 'crypto-derivatives',
                'name' => 'Cryptocurrency Derivatives',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'type' => 'course',
                'slug' => 'psychology-risk',
                'name' => 'Trading Psychology & Risk Management',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['type' => $cat['type'], 'slug' => $cat['slug']],
                $cat
            );
        }
    }
}
