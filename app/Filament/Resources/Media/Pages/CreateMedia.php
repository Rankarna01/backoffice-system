<?php

namespace App\Filament\Resources\Media\Pages;

use App\Domain\Media\Models\MediaAsset;
use App\Filament\Resources\Media\MediaResource;
use App\Services\CloudflareR2Service;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateMedia extends CreateRecord
{
    protected static string $resource = MediaResource::class;

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $uploadedRelativePath = $data['file_upload'] ?? null;
        $collection = $data['collection'] ?? 'general';
        $name = $data['name'];

        /** @var CloudflareR2Service $r2Service */
        $r2Service = app(CloudflareR2Service::class);

        $fileName = $uploadedRelativePath ? basename($uploadedRelativePath) : 'asset.bin';
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $diskPublic = Storage::disk('public');
        $fullPath = $uploadedRelativePath ? $diskPublic->path($uploadedRelativePath) : null;

        $sizeBytes = ($fullPath && file_exists($fullPath)) ? filesize($fullPath) : 1024 * 1024;
        $mimeType = ($fullPath && file_exists($fullPath)) ? (mime_content_type($fullPath) ?: 'application/octet-stream') : 'application/octet-stream';
        $type = $r2Service->detectMediaType($mimeType, $extension);

        $r2Path = trim($collection, '/') . '/' . Str::slug(pathinfo($name, PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . ($extension ?: 'bin');
        $publicUrl = $r2Service->generatePublicUrl($r2Path);

        // Save into mock public folder for local preview
        if ($fullPath && file_exists($fullPath)) {
            $diskPublic->put('r2-mock/' . $r2Path, file_get_contents($fullPath));
        }

        return MediaAsset::create([
            'name' => $name,
            'file_name' => $fileName,
            'file_path' => $r2Path,
            'disk' => 'r2',
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'type' => $type,
            'collection' => $collection,
            'alt_text' => $data['alt_text'] ?? null,
            'description' => $data['description'] ?? null,
            'public_url' => $publicUrl,
            'uploaded_by' => auth()->id(),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
