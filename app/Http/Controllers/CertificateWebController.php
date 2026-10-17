<?php

namespace App\Http\Controllers;

use App\Domain\Learning\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateWebController extends Controller
{
    /**
     * Display a full-screen, printable official digital certificate (A4 Landscape).
     */
    public function view(string $certificate_number): View
    {
        $certificate = Certificate::with(['user', 'course.mentor'])
            ->where('certificate_number', $certificate_number)
            ->firstOrFail();

        return view('certificates.digital-certificate', [
            'certificate' => $certificate,
        ]);
    }
}
