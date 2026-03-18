<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MessageAttachmentStorageService
{
    public function isEnabled(): bool
    {
        return (bool) config('azure_blob.enabled', false);
    }

    public function disk(): string
    {
        return (string) config('azure_blob.disk', 'azure_blob');
    }

    public function buildPath(string $originalName, ?int $tenantId = null): string
    {
        $prefix = trim((string) config('azure_blob.path_prefix', 'messages'), '/');
        $tenantSegment = $tenantId ? 'tenant-'.$tenantId : 'tenant-global';
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName) ?: 'file.bin';

        return $prefix.'/'.$tenantSegment.'/'.now()->format('Y/m/d').'/'.uniqid('', true).'-'.$safeName;
    }

    /**
     * Store uploaded file and return a URL/path pair for message persistence.
     */
    public function storeUploadedFile(UploadedFile $file, ?int $tenantId = null): array
    {
        $path = $this->buildPath($file->getClientOriginalName(), $tenantId);

        Storage::disk($this->disk())->put($path, $file->getContent());

        return [
            'path' => $path,
            'url' => $this->resolveUrl($path),
        ];
    }

    public function resolveUrl(string $path): string
    {
        $baseUrl = trim((string) config('azure_blob.public_base_url', ''), '/');

        if ($baseUrl !== '') {
            return $baseUrl.'/'.ltrim($path, '/');
        }

        return Storage::disk($this->disk())->url($path);
    }
}

