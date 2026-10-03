<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Quiz;
use App\Domain\Learning\Models\QuizAttempt;
use App\Domain\Learning\Models\QuizOption;
use App\Domain\Learning\Models\QuizQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display a specific quiz and its questions (without revealing correct answers).
     * GET /api/v1/quizzes/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $quiz = Quiz::with([
            'course:id,title,slug',
            'module:id,title',
            'questions' => function ($q) {
                $q->orderBy('sort_order', 'asc');
            },
            'questions.options' => function ($q) {
                $q->select('id', 'question_id', 'label', 'sort_order')->orderBy('sort_order', 'asc');
            },
        ])->find($id);

        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis evaluasi tidak ditemukan.',
            ], 404);
        }

        $data = [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'instructions' => $quiz->instructions ?? 'Jawab seluruh pertanyaan berikut untuk menguji pemahaman materi bab ini.',
            'time_limit_minutes' => $quiz->time_limit_minutes ?? 15,
            'passing_score' => (int) $quiz->passing_score,
            'course_title' => $quiz->course?->title,
            'module_title' => $quiz->module?->title,
            'questions' => $quiz->questions->map(function (QuizQuestion $question) {
                return [
                    'id' => $question->id,
                    'type' => $question->type,
                    'question' => $question->question,
                    'points' => $question->points,
                    'options' => $question->options->map(function (QuizOption $option) {
                        return [
                            'id' => $option->id,
                            'label' => $option->label,
                        ];
                    }),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Soal kuis berhasil dimuat.',
            'data' => $data,
        ]);
    }

    /**
     * Submit answers, calculate score, record attempt, and return immediate feedback.
     * POST /api/v1/quizzes/{id}/submit
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $quiz = Quiz::with('questions.options')->find($id);
        if (!$quiz) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis tidak ditemukan.',
            ], 404);
        }

        // Answers payload format: [{ "question_id": 1, "selected_option_id": 2 }, ...]
        $answers = $request->input('answers', []);
        $totalQuestions = $quiz->questions->count();
        if ($totalQuestions === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis belum memiliki pertanyaan.',
            ], 422);
        }

        $correctCount = 0;
        $feedback = [];

        foreach ($quiz->questions as $question) {
            $userAns = collect($answers)->firstWhere('question_id', $question->id);
            $selectedOptionId = $userAns ? ($userAns['selected_option_id'] ?? null) : null;

            $correctOption = $question->options->firstWhere('is_correct', true);
            $isCorrect = $correctOption && ((int)$selectedOptionId === (int)$correctOption->id);

            if ($isCorrect) {
                $correctCount++;
            }

            $feedback[] = [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation ?? ($isCorrect ? 'Jawaban Anda tepat!' : 'Kurang tepat. Periksa kembali struktur materi pada bab ini.'),
                'correct_option_id' => $quiz->show_answers ? $correctOption?->id : null,
            ];
        }

        $score = round(($correctCount / $totalQuestions) * 100);
        $isPassed = $score >= $quiz->passing_score;

        $previousAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'is_passed' => $isPassed,
            'attempt_number' => $previousAttempts + 1,
            'answers_payload' => $answers,
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $isPassed ? 'Selamat! Anda lulus evaluasi kuis ini.' : 'Belum mencapai batas kelulusan. Pelajari kembali materi dan coba lagi.',
            'data' => [
                'attempt_id' => $attempt->id,
                'score' => $score,
                'is_passed' => $isPassed,
                'passing_score' => (int) $quiz->passing_score,
                'correct_answers_count' => $correctCount,
                'total_questions_count' => $totalQuestions,
                'feedback' => $feedback,
            ],
        ]);
    }
}
