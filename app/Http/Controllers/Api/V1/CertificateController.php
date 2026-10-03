<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Learning\Models\Certificate;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
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
