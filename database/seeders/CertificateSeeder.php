<?php

namespace Database\Seeders;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $courses = Course::where('has_certificate', true)->get();

        if ($users->isEmpty() || $courses->isEmpty()) {
            return;
        }

        $sampleStudent = User::where('email', 'like', '%student%')
            ->orWhere('email', 'like', '%member%')
            ->first() ?: $users->first();

        // 1. Certificate for Course 1
        $course1 = $courses->first();
        if ($course1 && $sampleStudent) {
            $certNum1 = 'CERT-2026-TE-SMC889';
            $cert1 = Certificate::updateOrCreate(
                ['certificate_number' => $certNum1],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $sampleStudent->id,
                    'course_id' => $course1->id,
                    'student_name' => $sampleStudent->name,
                    'course_title' => $course1->title,
                    'issued_at' => now()->subDays(14),
                    'verification_url' => url('/verify/' . $certNum1),
                ]
            );

            // Ensure enrollment is 100%
            CourseEnrollment::updateOrCreate(
                [
                    'user_id' => $sampleStudent->id,
                    'course_id' => $course1->id,
                ],
                [
                    'progress_percentage' => 100.00,
                    'completed_at' => now()->subDays(14),
                    'enrolled_at' => now()->subDays(30),
                ]
            );
        }

        // 2. Certificate for Course 2 (if exists)
        if ($courses->count() > 1 && $sampleStudent) {
            $course2 = $courses->get(1);
            $certNum2 = 'CERT-2026-TE-GLD442';
            Certificate::updateOrCreate(
                ['certificate_number' => $certNum2],
                [
                    'uuid' => (string) Str::uuid(),
                    'user_id' => $sampleStudent->id,
                    'course_id' => $course2->id,
                    'student_name' => $sampleStudent->name,
                    'course_title' => $course2->title,
                    'issued_at' => now()->subDays(5),
                    'verification_url' => url('/verify/' . $certNum2),
                ]
            );

            CourseEnrollment::updateOrCreate(
                [
                    'user_id' => $sampleStudent->id,
                    'course_id' => $course2->id,
                ],
                [
                    'progress_percentage' => 100.00,
                    'completed_at' => now()->subDays(5),
                    'enrolled_at' => now()->subDays(20),
                ]
            );
        }
    }
}
