<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Community\Enums\DiscussionCategory;
use App\Domain\Community\Enums\DiscussionStatus;
use App\Domain\Community\Enums\ModerationStatus;
use App\Domain\Community\Models\Discussion;
use App\Domain\Community\Models\DiscussionReply;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    protected function resolveUser(Request $request): User
    {
        return $request->user() 
            ?? User::where('email', 'like', '%student%')->first() 
            ?? User::firstOrFail();
    }

    /**
     * List all discussions with filters, search, and category counts.
     * GET /api/v1/discussions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Discussion::with(['author:id,name,email', 'course:id,title,slug'])
            ->where('status', '!=', DiscussionStatus::Hidden->value)
            ->where('moderation_status', ModerationStatus::Approved->value);

        // Filter by category
        if ($cat = $request->query('category')) {
            if ($cat !== 'all') {
                $query->where('category', $cat);
            }
        }

        // Filter by course
        if ($courseId = $request->query('course_id')) {
            $query->where('course_id', $courseId);
        }

        // Search in title and content
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter tab: all, unanswered, my_discussions
        $tab = $request->query('tab', 'all');
        if ($tab === 'unanswered') {
            $query->where('replies_count', 0);
        } elseif ($tab === 'my_discussions') {
            $user = $request->user();
            if ($user) {
                $query->where('user_id', $user->id);
            }
        }

        // Sort: latest, popular, activity
        $sort = $request->query('sort', 'activity');
        if ($sort === 'latest') {
            $query->orderByDesc('is_pinned')->orderByDesc('created_at');
        } elseif ($sort === 'popular') {
            $query->orderByDesc('is_pinned')->orderByDesc('likes_count')->orderByDesc('replies_count');
        } else {
            // Default: pinned first, then newest activity
            $query->orderByDesc('is_pinned')->orderByDesc('last_activity_at');
        }

        $discussions = $query->paginate($request->query('per_page', 15));

        $formatted = $discussions->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => [
                    'value' => $item->category->value,
                    'label' => $item->category->getLabel(),
                    'color' => $item->category->getColor(),
                ],
                'content_snippet' => Str::limit(strip_tags($item->content), 160),
                'status' => $item->status->value,
                'is_pinned' => (bool) $item->is_pinned,
                'is_locked' => (bool) $item->is_locked,
                'views_count' => $item->views_count,
                'likes_count' => $item->likes_count,
                'replies_count' => $item->replies_count,
                'has_solution' => $item->replies()->where('is_solution', true)->exists(),
                'author' => [
                    'id' => $item->author?->id,
                    'name' => $item->author?->name ?? 'Anonymous Trader',
                    'email' => $item->author?->email,
                    'avatar' => null,
                    'role' => $item->author?->hasRole('admin') ? 'Admin' : ($item->author?->hasRole('mentor') ? 'Mentor' : 'Member'),
                ],
                'course' => $item->course ? [
                    'id' => $item->course->id,
                    'title' => $item->course->title,
                    'slug' => $item->course->slug,
                ] : null,
                'created_at' => $item->created_at->toISOString(),
                'last_activity_at' => $item->last_activity_at?->toISOString() ?? $item->created_at->toISOString(),
                'last_activity_formatted' => $item->last_activity_at?->diffForHumans() ?? $item->created_at->diffForHumans(),
            ];
        });

        // Compute global community statistics
        $stats = [
            'total_discussions' => Discussion::where('moderation_status', ModerationStatus::Approved->value)->count(),
            'total_replies' => DiscussionReply::where('is_hidden', false)->count(),
            'active_members' => User::count(),
            'solved_discussions' => DiscussionReply::where('is_solution', true)->distinct('discussion_id')->count('discussion_id'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Daftar diskusi berhasil dimuat.',
            'data' => $formatted,
            'meta' => [
                'current_page' => $discussions->currentPage(),
                'last_page' => $discussions->lastPage(),
                'per_page' => $discussions->perPage(),
                'total' => $discussions->total(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Get available discussion categories with count of topics.
     * GET /api/v1/discussions/categories
     */
    public function categories(): JsonResponse
    {
        $categories = collect(DiscussionCategory::cases())->map(function ($cat) {
            $count = Discussion::where('category', $cat->value)
                ->where('moderation_status', ModerationStatus::Approved->value)
                ->count();

            return [
                'value' => $cat->value,
                'label' => $cat->getLabel(),
                'color' => $cat->getColor(),
                'discussions_count' => $count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get thread details with replies.
     * GET /api/v1/discussions/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $discussion = Discussion::with(['author', 'course', 'rootReplies.author', 'rootReplies.children.author'])
            ->where('slug', $slug)
            ->first();

        if (!$discussion) {
            return response()->json([
                'success' => false,
                'message' => 'Topik diskusi tidak ditemukan.',
            ], 404);
        }

        // Increment views count
        $discussion->increment('views_count');

        $formatReply = function ($reply) use (&$formatReply) {
            return [
                'id' => $reply->id,
                'discussion_id' => $reply->discussion_id,
                'parent_id' => $reply->parent_id,
                'content' => $reply->content,
                'likes_count' => $reply->likes_count,
                'is_solution' => (bool) $reply->is_solution,
                'author' => [
                    'id' => $reply->author?->id,
                    'name' => $reply->author?->name ?? 'Member',
                    'email' => $reply->author?->email,
                    'role' => $reply->author?->hasRole('admin') ? 'Admin' : ($reply->author?->hasRole('mentor') ? 'Mentor' : 'Member'),
                ],
                'created_at' => $reply->created_at->toISOString(),
                'created_at_formatted' => $reply->created_at->diffForHumans(),
                'children' => $reply->children ? $reply->children->map($formatReply) : [],
            ];
        };

        $replies = $discussion->rootReplies->map($formatReply);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $discussion->id,
                'title' => $discussion->title,
                'slug' => $discussion->slug,
                'content' => $discussion->content,
                'category' => [
                    'value' => $discussion->category->value,
                    'label' => $discussion->category->getLabel(),
                    'color' => $discussion->category->getColor(),
                ],
                'status' => $discussion->status->value,
                'is_pinned' => (bool) $discussion->is_pinned,
                'is_locked' => (bool) $discussion->is_locked,
                'views_count' => $discussion->views_count,
                'likes_count' => $discussion->likes_count,
                'replies_count' => $discussion->replies_count,
                'author' => [
                    'id' => $discussion->author?->id,
                    'name' => $discussion->author?->name ?? 'Anonymous Trader',
                    'email' => $discussion->author?->email,
                    'role' => $discussion->author?->hasRole('admin') ? 'Admin' : ($discussion->author?->hasRole('mentor') ? 'Mentor' : 'Member'),
                ],
                'course' => $discussion->course ? [
                    'id' => $discussion->course->id,
                    'title' => $discussion->course->title,
                    'slug' => $discussion->course->slug,
                ] : null,
                'created_at' => $discussion->created_at->toISOString(),
                'created_at_formatted' => $discussion->created_at->format('d M Y, H:i'),
                'replies' => $replies,
            ],
        ]);
    }

    /**
     * Create a new discussion thread.
     * POST /api/v1/discussions
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:200',
            'content' => 'required|string|min:10',
            'category' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $categoryEnum = DiscussionCategory::tryFrom($validated['category']) ?? DiscussionCategory::General;

        $discussion = Discussion::create([
            'user_id' => $user->id,
            'course_id' => $validated['course_id'] ?? null,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $categoryEnum,
            'status' => DiscussionStatus::Published,
            'moderation_status' => ModerationStatus::Approved,
            'is_pinned' => false,
            'is_locked' => false,
            'views_count' => 1,
            'likes_count' => 0,
            'replies_count' => 0,
            'reports_count' => 0,
            'last_activity_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topik diskusi berhasil diterbitkan!',
            'data' => [
                'id' => $discussion->id,
                'slug' => $discussion->slug,
                'title' => $discussion->title,
            ],
        ], 201);
    }

    /**
     * Post a reply to a discussion thread.
     * POST /api/v1/discussions/{id}/replies
     */
    public function reply(Request $request, int $id): JsonResponse
    {
        $user = $this->resolveUser($request);
        $discussion = Discussion::findOrFail($id);

        if ($discussion->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Topik diskusi ini telah dikunci dan tidak menerima balasan baru.',
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|min:2',
            'parent_id' => 'nullable|exists:discussion_replies,id',
        ]);

        $reply = DiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'likes_count' => 0,
            'is_solution' => false,
            'is_hidden' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dikirim!',
            'data' => [
                'id' => $reply->id,
                'discussion_id' => $reply->discussion_id,
                'content' => $reply->content,
                'created_at' => $reply->created_at->toISOString(),
                'author' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ],
        ], 201);
    }

    /**
     * Like a discussion thread.
     * POST /api/v1/discussions/{id}/like
     */
    public function like(int $id): JsonResponse
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->increment('likes_count');

        return response()->json([
            'success' => true,
            'message' => 'Disukai',
            'likes_count' => $discussion->likes_count,
        ]);
    }

    /**
     * Like a reply.
     * POST /api/v1/discussions/replies/{id}/like
     */
    public function likeReply(int $id): JsonResponse
    {
        $reply = DiscussionReply::findOrFail($id);
        $reply->increment('likes_count');

        return response()->json([
            'success' => true,
            'message' => 'Balasan disukai',
            'likes_count' => $reply->likes_count,
        ]);
    }

    /**
     * Mark a reply as accepted solution.
     * POST /api/v1/discussions/{id}/solution/{replyId}
     */
    public function markSolution(int $id, int $replyId): JsonResponse
    {
        $discussion = Discussion::findOrFail($id);
        $reply = DiscussionReply::where('discussion_id', $discussion->id)->findOrFail($replyId);

        // Reset previous solutions on this thread
        DiscussionReply::where('discussion_id', $discussion->id)->update(['is_solution' => false]);

        $reply->update(['is_solution' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan ditandai sebagai solusi terbaik!',
        ]);
    }
}
