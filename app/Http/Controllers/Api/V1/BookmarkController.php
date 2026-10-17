<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Bookmark;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    protected function resolveUser(Request $request): User
    {
        return $request->user() 
            ?? User::where('email', 'budi@tradingedu.com')->first() 
            ?? User::firstOrFail();
    }

    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $query = Bookmark::where('user_id', $user->id)->latest();

        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->where('bookmarkable_type', $type);
            }
        }

        if ($request->boolean('with_notes')) {
            $query->whereNotNull('note')->where('note', '!=', '');
        }

        $bookmarks = $query->get()->map(function ($b) {
            return [
                'id' => $b->id,
                'bookmarkable_type' => $b->bookmarkable_type,
                'bookmarkable_id' => $b->bookmarkable_id,
                'note' => $b->note,
                'created_at' => $b->created_at->toIso8601String(),
                'details' => $b->details,
            ];
        });

        // Filter search on memory details if requested
        if ($search = $request->input('search')) {
            $bookmarks = $bookmarks->filter(function ($item) use ($search) {
                $title = $item['details']['title'] ?? '';
                $course = $item['details']['course_title'] ?? '';
                $note = $item['note'] ?? '';
                return str_contains(strtolower($title), strtolower($search)) ||
                       str_contains(strtolower($course), strtolower($search)) ||
                       str_contains(strtolower($note), strtolower($search));
            })->values();
        }

        return response()->json([
            'success' => true,
            'data' => $bookmarks,
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $all = Bookmark::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $all->count(),
                'total_lessons' => $all->where('bookmarkable_type', 'lesson')->count(),
                'total_courses' => $all->where('bookmarkable_type', 'course')->count(),
                'total_notes' => $all->filter(fn($b) => !empty($b->note))->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->resolveUser($request);

        $validated = $request->validate([
            'bookmarkable_type' => 'required|in:course,lesson',
            'bookmarkable_id' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        $bookmark = Bookmark::updateOrCreate(
            [
                'user_id' => $user->id,
                'bookmarkable_type' => $validated['bookmarkable_type'],
                'bookmarkable_id' => $validated['bookmarkable_id'],
            ],
            [
                'note' => $validated['note'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil disimpan ke bookmark.',
            'data' => [
                'id' => $bookmark->id,
                'bookmarkable_type' => $bookmark->bookmarkable_type,
                'bookmarkable_id' => $bookmark->bookmarkable_id,
                'note' => $bookmark->note,
                'details' => $bookmark->details,
            ],
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        $bookmark = Bookmark::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'note' => 'nullable|string',
        ]);

        $bookmark->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catatan belajar berhasil diperbarui.',
            'data' => [
                'id' => $bookmark->id,
                'note' => $bookmark->note,
                'details' => $bookmark->details,
            ],
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $this->resolveUser($request);

        $bookmark = Bookmark::where('user_id', $user->id)->findOrFail($id);
        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dihapus dari bookmark.',
        ]);
    }
}
