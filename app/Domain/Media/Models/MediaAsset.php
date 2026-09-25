<?php

namespace App\Domain\Media\Models;

use App\Models\User;
use App\Services\CloudflareR2Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_assets';

    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'disk',
        'mime_type',
        'size_bytes',
        'type',
        'collection',
        'alt_text',
        'description',
        'public_url',
        'metadata',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getSizeFormattedAttribute(): string
    {
        return CloudflareR2Service::formatBytes($this->size_bytes ?? 0);
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'video' => 'danger',
            'image' => 'info',
            'document' => 'warning',
            'audio' => 'primary',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'video' => 'heroicon-m-video-camera',
            'image' => 'heroicon-m-photo',
            'document' => 'heroicon-m-document-text',
            'audio' => 'heroicon-m-speaker-wave',
            default => 'heroicon-m-paper-clip',
        };
    }

    public function getR2CdnUrlAttribute(): string
    {
        if (! empty($this->public_url)) {
            return $this->public_url;
        }

        /** @var CloudflareR2Service $r2 */
        $r2 = app(CloudflareR2Service::class);
        return $r2->generatePublicUrl($this->file_path);
    }

    public function scopeVideos(Builder $query): Builder
    {
        return $query->where('type', 'video');
    }

    public function scopeImages(Builder $query): Builder
    {
        return $query->where('type', 'image');
    }

    public function scopeDocuments(Builder $query): Builder
    {
        return $query->where('type', 'document');
    }

    public function scopeCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }
}
