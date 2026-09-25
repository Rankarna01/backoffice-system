<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Modularized per feature / module according to backoffice architecture:
     *
     * 1. RoleAndPermissionSeeder : Roles & Spatie permissions matrix
     * 2. UserSeeder               : Super Admin, Staff, and Customer users
     * 3. MentorSeeder             : Mentors & detailed Mentor Profiles
     * 4. CategorySeeder           : Course Categories & taxonomies
     * 5. CourseSeeder             : Courses & syllabus configurations
     * 6. ModuleSeeder             : Modules / Bab with PDF, PPT, and Excel documents
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            MentorSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            ModuleSeeder::class,
            LessonSeeder::class,
            QuizSeeder::class,
            MediaAssetSeeder::class,
            SignalSeeder::class,
        ]);
    }
}
