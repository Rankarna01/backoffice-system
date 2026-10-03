<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\LessonProgress;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyCourseController extends Controller
{
    /**
     * Display a listing of courses enrolled by the authenticated user.
     * GET /api/v1/my-courses
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // If no enrollments exist yet, auto-enroll into first published course for seamless trial experience
        $enrollmentsCount = CourseEnrollment::where('user_id', $user->id)->count();
        if ($enrollmentsCount === 0) {
            $firstCourse = Course::where('status', 'published')->first();
            if ($firstCourse) {
                CourseEnrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $firstCourse->id,
                    'progress_percentage' => 0.00,
                    'completed_lessons_count' => 0,
                    'enrolled_at' => now(),
                ]);
            }
        }

        $enrollments = CourseEnrollment::with(['course.category', 'course.mentor'])
            ->where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        $defaultThumbnail = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        $data = $enrollments->map(function (CourseEnrollment $enrollment) use ($user, $defaultThumbnail) {
            $course = $enrollment->course;
            if (!$course) return null;

            // Find last accessed or next incomplete lesson
            $lastProgress = LessonProgress::where('user_id', $user->id)
                ->whereHas('lesson', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })
                ->orderBy('updated_at', 'desc')
                ->first();

            $lastLesson = null;
            if ($lastProgress && $lastProgress->lesson) {
                $lastLesson = [
                    'uuid' => $lastProgress->lesson->uuid,
                    'title' => $lastProgress->lesson->title,
                ];
            } else {
                $firstLesson = Lesson::where('course_id', $course->id)->orderBy('sort_order', 'asc')->first();
                if ($firstLesson) {
                    $lastLesson = [
                        'uuid' => $firstLesson->uuid,
                        'title' => $firstLesson->title,
                    ];
                }
            }

            // Check if certificate issued
            $cert = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'course_slug' => $course->slug,
                'level' => $course->level,
                'category_name' => $course->category?->name ?? 'Trading Strategy',
                'thumbnail_url' => $defaultThumbnail,
                'enrolled_at' => $enrollment->enrolled_at?->toIso8601String() ?? now()->toIso8601String(),
                'progress_percentage' => (float) $enrollment->progress_percentage,
                'completed_lessons_count' => (int) $enrollment->completed_lessons_count,
                'total_lessons_count' => (int) ($course->lessons_count ?: $course->lessons()->count()),
                'last_accessed_lesson' => $lastLesson,
                'certificate' => $cert ? [
                    'certificate_number' => $cert->certificate_number,
                    'issued_at' => $cert->issued_at?->toIso8601String(),
                    'verification_url' => $cert->verification_url,
                ] : null,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kelas murid berhasil dimuat.',
            'data' => $data,
        ]);
    }

    /**
     * Display the classroom dashboard with syllabus completion states.
     * GET /api/v1/courses/{slug}/classroom
     */
    public function classroom(Request $request, string $slug): JsonResponse
    {
        $user = $request->user();

        $course = Course::with([
            'modules' => function ($q) {
                $q->where('is_published', true)->orderBy('sort_order', 'asc');
            },
            'modules.lessons' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            },
            'modules.quizzes' => function ($q) {
                $q->where('status', 'published');
            },
        ])->where('slug', $slug)->first();

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Kursus tidak ditemukan.',
            ], 404);
        }

        // Get or create enrollment for trial convenience
        $enrollment = CourseEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress_percentage' => 0.00, 'completed_lessons_count' => 0, 'enrolled_at' => now()]
        );

        // Fetch completed lesson IDs for this user
        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $isSequential = (bool) $course->is_sequential;
        $previousCompleted = true; // First lesson is always unlocked

        $modules = $course->modules->map(function ($mod) use ($completedLessonIds, $isSequential, &$previousCompleted) {
            $lessons = $mod->lessons->map(function ($les) use ($completedLessonIds, $isSequential, &$previousCompleted) {
                $isCompleted = in_array($les->id, $completedLessonIds);
                $isLocked = $isSequential && !$previousCompleted && !$isCompleted && !$les->is_preview;

                if ($isSequential && !$isCompleted && !$les->is_preview) {
                    $previousCompleted = false;
                }

                return [
                    'id' => $les->id,
                    'uuid' => $les->uuid,
                    'title' => $les->title,
                    'type' => $les->type,
                    'duration_seconds' => $les->duration_seconds ?? 600,
                    'is_completed' => $isCompleted,
                    'is_locked' => $isLocked,
                    'is_preview' => (bool) $les->is_preview,
                ];
            });

            return [
                'id' => $mod->id,
                'title' => $mod->title,
                'description' => $mod->description,
                'lessons' => $lessons,
                'quiz' => $mod->quizzes->first() ? [
                    'id' => $mod->quizzes->first()->id,
                    'title' => $mod->quizzes->first()->title,
                    'passing_score' => $mod->quizzes->first()->passing_score,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Ruang belajar berhasil dimuat.',
            'data' => [
                'course_id' => $course->id,
                'course_uuid' => $course->uuid,
                'course_title' => $course->title,
                'course_slug' => $course->slug,
                'progress_percentage' => (float) $enrollment->progress_percentage,
                'completed_lessons_count' => (int) $enrollment->completed_lessons_count,
                'total_lessons_count' => $course->lessons()->count(),
                'modules' => $modules,
            ],
        ]);
    }
}
