<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds for Users (Super Admin, Staff, Customers).
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // 1. Super Admin User
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
        if ($superAdminRole) {
            $superAdmin->syncRoles([$superAdminRole]);
        }

        // 2. Operational Admin User (Staff)
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
        if ($adminRole) {
            $operationalAdmin->syncRoles([$adminRole]);
        }

        // 3. Customer / Member Accounts
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
        if ($customerRole) {
            $customer1->syncRoles([$customerRole]);
        }

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
        if ($customerRole) {
            $customer2->syncRoles([$customerRole]);
        }
    }
}
