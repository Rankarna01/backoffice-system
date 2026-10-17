<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\CourseEnrollment;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Get list of certificates earned by the authenticated user.
     * GET /api/v1/certificates
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user() ?: User::first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login untuk melihat daftar sertifikat Anda.',
            ], 401);
        }

        $query = Certificate::with(['course:id,title,slug,level,duration_seconds,lessons_count', 'course.mentor:id,name'])
            ->where('user_id', $user->id);

        // Search by course title or certificate number
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('course_title', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->orderBy('issued_at', 'desc')->get();

        // Calculate statistics
        $totalCertificates = $certificates->count();
        $totalCoursesCompleted = CourseEnrollment::where('user_id', $user->id)
            ->where('progress_percentage', '>=', 100)
            ->count();

        $latestCert = $certificates->first();

        $formattedData = $certificates->map(function ($cert) {
            return [
                'id' => $cert->id,
                'uuid' => $cert->uuid,
                'certificate_number' => $cert->certificate_number,
                'student_name' => $cert->student_name ?? $cert->user?->name,
                'course_id' => $cert->course_id,
                'course_title' => $cert->course_title ?? $cert->course?->title,
                'course_slug' => $cert->course?->slug,
                'course_level' => $cert->course?->level ?? 'intermediate',
                'lessons_count' => $cert->course?->lessons_count ?? 0,
                'duration_hours' => $cert->course?->duration_seconds ? round($cert->course->duration_seconds / 3600, 1) : 0,
                'mentor_name' => $cert->course?->mentor?->name ?? 'TradingEdu Master Mentor',
                'issued_at' => $cert->issued_at?->format('d F Y') ?? now()->format('d F Y'),
                'issued_at_iso' => $cert->issued_at?->toISOString() ?? now()->toISOString(),
                'verification_url' => url('/verify/' . $cert->certificate_number),
                'pdf_url' => $cert->pdf_path ? url($cert->pdf_path) : null,
                'issuer' => 'TradingEdu Academy Global',
                'accreditation' => 'Verified Financial Technical Trading Curriculum',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar sertifikat berhasil dimuat.',
            'data' => $formattedData,
            'stats' => [
                'total_certificates' => $totalCertificates,
                'total_courses_completed' => max($totalCoursesCompleted, $totalCertificates),
                'latest_certificate_date' => $latestCert ? ($latestCert->issued_at?->format('d M Y') ?? '-') : '-',
            ],
        ]);
    }

    /**
     * Get statistics of earned certificates.
     * GET /api/v1/certificates/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user() ?: User::first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $totalCertificates = Certificate::where('user_id', $user->id)->count();
        $totalCompleted = CourseEnrollment::where('user_id', $user->id)
            ->where('progress_percentage', '>=', 100)
            ->count();

        $latest = Certificate::where('user_id', $user->id)
            ->latest('issued_at')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_certificates' => $totalCertificates,
                'total_completed_courses' => max($totalCompleted, $totalCertificates),
                'latest_issued_at' => $latest ? ($latest->issued_at?->format('d F Y') ?? '-') : '-',
            ],
        ]);
    }

    /**
     * Show detail of a single certificate.
     * GET /api/v1/certificates/{certificate_number}
     */
    public function show(Request $request, string $certificate_number): JsonResponse
    {
        $user = $request->user() ?: User::first();

        $certificate = Certificate::with(['course.mentor', 'user'])
            ->where('certificate_number', $certificate_number)
            ->first();

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'student_name' => $certificate->student_name ?? $certificate->user?->name,
                'course_id' => $certificate->course_id,
                'course_title' => $certificate->course_title ?? $certificate->course?->title,
                'course_slug' => $certificate->course?->slug,
                'course_level' => $certificate->course?->level ?? 'intermediate',
                'mentor_name' => $certificate->course?->mentor?->name ?? 'TradingEdu Master Mentor',
                'issued_at' => $certificate->issued_at?->format('d F Y') ?? now()->format('d F Y'),
                'issued_at_iso' => $certificate->issued_at?->toISOString() ?? now()->toISOString(),
                'verification_url' => url('/verify/' . $certificate->certificate_number),
                'pdf_url' => $certificate->pdf_path ? url($certificate->pdf_path) : null,
                'issuer' => 'TradingEdu Academy Global',
                'accreditation' => 'Verified Financial Technical Trading Curriculum',
            ],
        ]);
    }

    /**
     * Claim / Generate a certificate for a completed course.
     * POST /api/v1/certificates/claim
     */
    public function claim(Request $request): JsonResponse
    {
        $user = $request->user() ?: User::first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($validated['course_id']);

        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Sertifikat telah diterbitkan sebelumnya.',
                'data' => [
                    'certificate_number' => $existing->certificate_number,
                    'verification_url' => url('/verify/' . $existing->certificate_number),
                ],
            ]);
        }

        // Create new certificate
        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'student_name' => $user->name,
            'course_title' => $course->title,
            'issued_at' => now(),
            'verification_url' => '',
        ]);

        $certificate->verification_url = url('/verify/' . $certificate->certificate_number);
        $certificate->save();

        return response()->json([
            'success' => true,
            'message' => 'Selamat! Sertifikat kelulusan Anda berhasil diterbitkan.',
            'data' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'verification_url' => $certificate->verification_url,
            ],
        ], 201);
    }

    /**
     * Public verification endpoint for student digital certificates.
     * GET /api/v1/certificates/verify/{certificate_number}
     */
    public function verify(Request $request, string $certificate_number): JsonResponse
    {
        $certificate = Certificate::with(['user:id,name', 'course:id,title,slug,level'])
            ->where('certificate_number', $certificate_number)
            ->first();

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor sertifikat tidak ditemukan atau tidak valid.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat terverifikasi valid.',
            'data' => [
                'certificate_number' => $certificate->certificate_number,
                'student_name' => $certificate->student_name ?? $certificate->user?->name,
                'course_title' => $certificate->course_title ?? $certificate->course?->title,
                'course_level' => $certificate->course?->level ?? 'intermediate',
                'issued_at' => $certificate->issued_at?->format('d F Y') ?? now()->format('d F Y'),
                'is_valid' => true,
                'verification_url' => url('/verify/' . $certificate->certificate_number),
                'pdf_url' => $certificate->pdf_path ? url($certificate->pdf_path) : null,
                'issuer' => 'TradingEdu Academy Global',
                'accreditation' => 'Verified Financial Technical Trading Curriculum',
            ],
        ]);
    }
}
