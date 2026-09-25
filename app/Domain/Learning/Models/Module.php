<?php

namespace App\Domain\Learning\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'document_file',
        'sort_order',
        'is_published',
    ];

    protected $appends = [
        'document_url',
        'document_extension',
        'document_type_label',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Module $module) {
            if (! $module->sort_order && $module->course_id) {
                $maxOrder = static::where('course_id', $module->course_id)->max('sort_order') ?? 0;
                $module->sort_order = $maxOrder + 1;
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getDocumentUrlAttribute(): ?string
    {
        if (! $this->document_file) {
            return null;
        }

        return asset('storage/' . $this->document_file);
    }

    public function getDocumentExtensionAttribute(): ?string
    {
        if (! $this->document_file) {
            return null;
        }

        return strtolower(pathinfo($this->document_file, PATHINFO_EXTENSION));
    }

    public function getDocumentTypeLabelAttribute(): ?string
    {
        return match ($this->document_extension) {
            'pdf' => 'PDF',
            'ppt', 'pptx' => 'PowerPoint',
            'xls', 'xlsx', 'csv' => 'Excel',
            default => $this->document_extension ? strtoupper($this->document_extension) : null,
        };
    }
}
