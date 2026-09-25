<?php

namespace Database\Seeders;

use App\Domain\Identity\Models\MentorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MentorSeeder extends Seeder
{
    /**
     * Run the database seeds for Mentors and their Profiles.
     */
    public function run(): void
    {
        $mentorRole = Role::where('name', 'mentor')->first();

        // 1. Mentor Alex Wijaya (Master Mentor)
        $mentor1 = User::updateOrCreate(
            ['email' => 'alex@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Alex Wijaya (Master Mentor)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628133333333',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        if ($mentorRole) {
            $mentor1->syncRoles([$mentorRole]);
        }

        MentorProfile::updateOrCreate(
            ['user_id' => $mentor1->id],
            [
                'headline' => 'Certified Financial Technician & Full-Time Price Action Trader',
                'bio' => 'Pengalaman 10+ tahun trading di pasar Forex dan Indeks Global. Spesialis analisa struktur pasar, likuiditas institusional, dan manajemen risiko ketat.',
                'expertise' => ['Price Action', 'Forex Major Pairs', 'Smart Money Concepts', 'Risk Management'],
                'social_links' => [
                    'instagram' => '@alexwijaya_fx',
                    'youtube' => 'AlexWijayaTrading',
                    'telegram' => 't.me/alexwijayafx',
                ],
                'is_featured' => true,
                'sort_order' => 1,
            ]
        );

        // 2. Mentor Sarah Tan (Commodities & Crypto Mentor)
        $mentor2 = User::updateOrCreate(
            ['email' => 'sarah@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Sarah Tan (Commodities & Crypto Mentor)',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628144444444',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        if ($mentorRole) {
            $mentor2->syncRoles([$mentorRole]);
        }

        MentorProfile::updateOrCreate(
            ['user_id' => $mentor2->id],
            [
                'headline' => 'Gold (XAUUSD) & Crypto Derivatives Specialist',
                'bio' => 'Fokus pada strategi swing & momentum di instrumen Emas (XAUUSD), Minyak Mentah, dan aset kripto utama dengan integrasi sentimen makro.',
                'expertise' => ['Gold (XAUUSD)', 'Cryptocurrency', 'Order Flow Analysis', 'Macro Economics'],
                'social_links' => [
                    'instagram' => '@sarahtan_trader',
                    'youtube' => 'SarahTanCommodities',
                ],
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );
    }
}
