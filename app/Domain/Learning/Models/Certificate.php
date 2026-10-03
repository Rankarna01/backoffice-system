<?php

namespace App\Domain\Learning\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'certificate_number',
        'user_id',
        'course_id',
        'student_name',
        'course_title',
        'issued_at',
        'verification_url',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($cert) {
            if (empty($cert->uuid)) {
                $cert->uuid = (string) Str::uuid();
            }
            if (empty($cert->certificate_number)) {
                $cert->certificate_number = 'CERT-' . date('Y') . '-TE-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
