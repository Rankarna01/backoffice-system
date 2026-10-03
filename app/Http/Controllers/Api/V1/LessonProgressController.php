<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Domain\Learning\Models\Lesson;
use App\Domain\Learning\Models\LessonProgress;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    /**
     * Display the streaming details for a specific lesson.
     * GET /api/v1/lessons/{uuid}
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        $user = $request->user();

        $lesson = Lesson::with(['course', 'module'])->where('uuid', $uuid)->first();
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Materi pelajaran tidak ditemukan.',
            ], 404);
        }

        // Fetch user progress for this lesson
        $progress = null;
        if ($user) {
            $progress = LessonProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();
        }

        // Determine prev and next lesson in course order
        $allCourseLessons = Lesson::where('course_id', $lesson->course_id)
            ->orderBy('sort_order', 'asc')
            ->get(['id', 'uuid', 'title']);

        $currentIndex = $allCourseLessons->search(function ($item) use ($lesson) {
            return $item->id === $lesson->id;
        });

        $prevLesson = ($currentIndex !== false && $currentIndex > 0)
            ? $allCourseLessons->get($currentIndex - 1)
            : null;

        $nextLesson = ($currentIndex !== false && $currentIndex < $allCourseLessons->count() - 1)
            ? $allCourseLessons->get($currentIndex + 1)
            : null;

        // Default video stream if not specified
        $videoStreamUrl = $lesson->video_ref;
        if (empty($videoStreamUrl) || !filter_var($videoStreamUrl, FILTER_VALIDATE_URL)) {
            $videoStreamUrl = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
        }

        $data = [
            'uuid' => $lesson->uuid,
            'title' => $lesson->title,
            'type' => $lesson->type ?? 'video',
            'video_provider' => $lesson->video_provider ?? 'stream',
            'video_stream_url' => $videoStreamUrl,
            'duration_seconds' => $lesson->duration_seconds ?? 600,
            'content' => $lesson->content ?? "Rangkuman materi pembahasan likuiditas dan eksekusi analisa teknikal market finansial.",
            'course' => [
                'id' => $lesson->course?->id,
                'title' => $lesson->course?->title,
                'slug' => $lesson->course?->slug,
            ],
            'module' => [
                'id' => $lesson->module?->id,
                'title' => $lesson->module?->title,
            ],
            'user_progress' => [
                'last_watched_seconds' => $progress ? $progress->last_watched_seconds : 0,
                'is_completed' => $progress ? (bool) $progress->is_completed : false,
            ],
            'attachments' => [
                [
                    'name' => 'PineScript SMC Template.txt',
                    'size' => '24 KB',
                    'url' => 'https://raw.githubusercontent.com/tradingedu/tools/main/smc-indicator.pine',
                ],
                [
                    'name' => 'Cheatsheet Anatomi Candle & Liquidity.pdf',
                    'size' => '1.2 MB',
                    'url' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                ],
            ],
            'prev_lesson' => $prevLesson ? [
                'uuid' => $prevLesson->uuid,
                'title' => $prevLesson->title,
            ] : null,
            'next_lesson' => $nextLesson ? [
                'uuid' => $nextLesson->uuid,
                'title' => $nextLesson->title,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dimuat.',
            'data' => $data,
        ]);
    }

    /**
     * Auto-save lesson watch progress and mark as completed.
     * POST /api/v1/lessons/{uuid}/progress
     */
    public function updateProgress(Request $request, string $uuid): JsonResponse
    {
        $user = $request->user();

        $lesson = Lesson::with('course')->where('uuid', $uuid)->first();
        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Materi pelajaran tidak ditemukan.',
            ], 404);
        }

        $lastWatched = (int) $request->input('last_watched_seconds', 0);
        $isCompleted = (bool) $request->input('is_completed', false);

        $progress = LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'last_watched_seconds' => $lastWatched,
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
            ]
        );

        // Recalculate enrollment progress
        $totalLessons = Lesson::where('course_id', $lesson->course_id)->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson', function ($q) use ($lesson) {
                $q->where('course_id', $lesson->course_id);
            })
            ->where('is_completed', true)
            ->count();

        $percentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0;
        $isCourseCompleted = $percentage >= 100;

        $enrollment = CourseEnrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $lesson->course_id],
            [
                'progress_percentage' => $percentage,
                'completed_lessons_count' => $completedLessons,
                'completed_at' => $isCourseCompleted ? now() : null,
            ]
        );

        // Auto-generate certificate if completed 100% and course has certificate
        $certificate = null;
        if ($isCourseCompleted && $lesson->course?->has_certificate) {
            $certificate = Certificate::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $lesson->course_id],
                [
                    'student_name' => $user->name,
                    'course_title' => $lesson->course->title,
                    'issued_at' => now(),
                    'verification_url' => url('/verify/CERT-' . date('Y') . '-TE-' . strtoupper(substr(md5($user->id . $lesson->course_id), 0, 6))),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Progres tontonan berhasil disimpan.',
            'data' => [
                'lesson_uuid' => $lesson->uuid,
                'is_completed' => $progress->is_completed,
                'course_progress_percentage' => (float) $percentage,
                'is_course_completed' => $isCourseCompleted,
                'certificate_number' => $certificate?->certificate_number,
            ],
        ]);
    }
}
