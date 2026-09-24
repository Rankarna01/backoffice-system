<?php

namespace Database\Seeders;

use App\Domain\Identity\Models\MentorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Permissions
        $permissions = [
            'view-dashboard',
            'manage-users',
            'manage-courses',
            'manage-signals',
            'manage-orders',
            'manage-community',
            'manage-settings',
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Create Roles per 06-Database-Schema.md: super_admin, admin, mentor, customer
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $mentorRole = Role::firstOrCreate(['name' => 'mentor', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $superAdminRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            'view-dashboard',
            'manage-users',
            'manage-courses',
            'manage-signals',
            'manage-orders',
            'manage-community',
        ]);

        $mentorRole->syncPermissions([
            'view-dashboard',
            'manage-courses',
            'manage-signals',
            'manage-community',
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);

        $customerRole->syncPermissions([
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);

        // 4. Seed Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Super Admin TradingEdu',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628111111111',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        // 5. Seed Operational Admin User
        $operationalAdmin = User::updateOrCreate(
            ['email' => 'staff@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Admin Operasional',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628122222222',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $operationalAdmin->syncRoles([$adminRole]);

        // 6. Seed Mentors with Profiles
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
        $mentor1->syncRoles([$mentorRole]);

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
        $mentor2->syncRoles([$mentorRole]);

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

        // 7. Seed Customer / Member Accounts
        $customer1 = User::updateOrCreate(
            ['email' => 'customer@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628155555555',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $customer1->syncRoles([$customerRole]);

        $customer2 = User::updateOrCreate(
            ['email' => 'dewi@tradingedu.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Dewi Lestari',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'phone' => '+628166666666',
                'timezone' => 'Asia/Jakarta',
                'locale' => 'id',
                'status' => 'active',
                'terms_accepted_version' => '1.0',
            ]
        );
        $customer2->syncRoles([$customerRole]);
    }
}
