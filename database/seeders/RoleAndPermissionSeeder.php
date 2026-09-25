<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define permissions per 01-Business-Flow.md & 03-Backend-Architecture.md
        $permissions = [
            // General / Dashboard
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

            // User Management
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'mentors.manage',
            'roles.manage',

            // Learning & Courses
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',
            'courses.publish',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'certificates.manage',

            // Market & Signals
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.delete',
            'signals.publish',
            'market_outlooks.manage',
            'news.manage',
            'economic_calendar.manage',

            // Community & Live Sessions
            'discussions.manage',
            'live_sessions.manage',

            // Monetization & Sales
            'orders.view',
            'orders.update',
            'orders.refund',
            'subscriptions.manage',
            'coupons.manage',
            'affiliates.manage',

            // Content & Website
            'announcements.manage',
            'reviews.manage',
            'testimonials.manage',
            'faqs.manage',
            'landing_page.manage',

            // System & Settings
            'settings.view',
            'settings.update',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Create Roles: super_admin, admin, mentor, customer
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
            'users.view',
            'users.create',
            'users.update',
            'mentors.manage',
            'courses.view',
            'courses.create',
            'courses.update',
            'courses.delete',
            'courses.publish',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'certificates.manage',
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.publish',
            'market_outlooks.manage',
            'news.manage',
            'economic_calendar.manage',
            'discussions.manage',
            'live_sessions.manage',
            'orders.view',
            'orders.update',
            'orders.refund',
            'subscriptions.manage',
            'coupons.manage',
            'affiliates.manage',
            'announcements.manage',
            'reviews.manage',
            'testimonials.manage',
            'faqs.manage',
            'landing_page.manage',
            'settings.view',
        ]);

        $mentorRole->syncPermissions([
            'view-dashboard',
            'manage-courses',
            'manage-signals',
            'manage-community',
            'courses.view',
            'courses.create',
            'courses.update',
            'modules.manage',
            'lessons.manage',
            'quizzes.manage',
            'signals.view',
            'signals.create',
            'signals.update',
            'signals.publish',
            'market_outlooks.manage',
            'live_sessions.manage',
            'discussions.manage',
            'reviews.manage',
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);

        $customerRole->syncPermissions([
            'access-learning',
            'access-trading-tools',
            'access-market-signals',
        ]);
    }
}
