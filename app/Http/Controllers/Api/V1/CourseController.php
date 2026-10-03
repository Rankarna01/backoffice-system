<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses for the catalog.
     * GET /api/v1/courses
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()
            ->with(['mentor:id,name,email', 'category:id,name,slug'])
            ->where('status', 'published');

        // Filter by category slug
        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by level
        if ($request->filled('level') && in_array($request->level, ['beginner', 'intermediate', 'advanced'])) {
            $query->where('level', $request->level);
        }

        // Filter by access type
        if ($request->filled('access_type') && in_array($request->access_type, ['free', 'paid', 'subscription'])) {
            $query->where('access_type', $request->access_type);
        }

        // Search in title, subtitle, description
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('subtitle', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('students_count', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $perPage = min((int) $request->get('per_page', 9), 30);
        $paginated = $query->paginate($perPage);

        $defaultThumbnail = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80';

        $items = collect($paginated->items())->map(function (Course $course) use ($defaultThumbnail) {
            return [
                'id' => $course->id,
                'uuid' => $course->uuid,
                'title' => $course->title,
                'slug' => $course->slug,
                'subtitle' => $course->subtitle,
                'level' => $course->level,
                'access_type' => $course->access_type,
                'price' => (float) $course->price,
                'compare_at_price' => $course->compare_at_price ? (float) $course->compare_at_price : null,
                'currency' => $course->currency ?? 'IDR',
                'thumbnail_url' => $defaultThumbnail,
                'duration_seconds' => $course->duration_seconds,
                'lessons_count' => $course->lessons_count,
                'students_count' => $course->students_count,
                'rating_avg' => (float) $course->rating_avg,
                'rating_count' => $course->rating_count,
                'mentor' => [
                    'id' => $course->mentor?->id,
                    'name' => $course->mentor?->name ?? 'TradingEdu Mentor',
                    'title' => 'Senior Trading Specialist',
                    'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80',
                ],
                'category' => $course->category ? [
                    'id' => $course->category->id,
                    'name' => $course->category->name,
                    'slug' => $course->category->slug,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Katalog kursus berhasil dimuat.',
            'data' => $items,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Display a specific course with full syllabus.
     * GET /api/v1/courses/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $course = Course::with([
            'mentor:id,name,email',
            'category:id,name,slug',
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

        // Check enrollment if authenticated
        $isEnrolled = false;
        $user = $request->user('sanctum');
        if ($user) {
            $isEnrolled = CourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();
        }

        $defaultThumbnail = 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=1200&auto=format&fit=crop&q=80';

        $data = [
            'id' => $course->id,
            'uuid' => $course->uuid,
            'title' => $course->title,
            'slug' => $course->slug,
            'subtitle' => $course->subtitle,
            'description' => $course->description,
            'preview_video_ref' => $course->preview_video_ref ?? 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'level' => $course->level,
            'access_type' => $course->access_type,
            'price' => (float) $course->price,
            'compare_at_price' => $course->compare_at_price ? (float) $course->compare_at_price : null,
            'currency' => $course->currency ?? 'IDR',
            'thumbnail_url' => $defaultThumbnail,
            'has_certificate' => (bool) $course->has_certificate,
            'is_sequential' => (bool) $course->is_sequential,
            'is_enrolled' => $isEnrolled,
            'duration_seconds' => $course->duration_seconds,
            'lessons_count' => $course->lessons_count,
            'students_count' => $course->students_count,
            'rating_avg' => (float) $course->rating_avg,
            'rating_count' => $course->rating_count,
            'mentor' => [
                'id' => $course->mentor?->id,
                'name' => $course->mentor?->name ?? 'TradingEdu Specialist',
                'title' => 'Senior Trading Specialist & Analyst',
                'bio' => 'Praktisi trader independen dengan jam terbang lebih dari 8 tahun dalam menganalisis pasar Forex dan Likuiditas Pasar Finansial.',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=160&auto=format&fit=crop&q=80',
            ],
            'category' => $course->category ? [
                'id' => $course->category->id,
                'name' => $course->category->name,
                'slug' => $course->category->slug,
            ] : null,
            'modules' => $course->modules->map(function ($mod) {
                return [
                    'id' => $mod->id,
                    'title' => $mod->title,
                    'description' => $mod->description,
                    'sort_order' => $mod->sort_order,
                    'document_file_url' => $mod->document_file ? url($mod->document_file) : null,
                    'lessons' => $mod->lessons->map(function ($les) {
                        return [
                            'id' => $les->id,
                            'uuid' => $les->uuid,
                            'title' => $les->title,
                            'type' => $les->type,
                            'duration_seconds' => $les->duration_seconds ?? 600,
                            'is_preview' => (bool) $les->is_preview,
                            'sort_order' => $les->sort_order,
                        ];
                    }),
                    'quiz' => $mod->quizzes->first() ? [
                        'id' => $mod->quizzes->first()->id,
                        'title' => $mod->quizzes->first()->title,
                        'passing_score' => $mod->quizzes->first()->passing_score,
                        'time_limit_minutes' => $mod->quizzes->first()->time_limit_minutes ?? 15,
                    ] : null,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail kursus berhasil dimuat.',
            'data' => $data,
        ]);
    }
}
