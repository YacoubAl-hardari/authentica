<?php

namespace Authentica\LaravelAuthentica\Traits;

trait HandlesMedia
{
    protected function validateMedia(string $userId, string $registered, string $query, string $type): void
    {
        if (empty($userId)) {
            throw new \InvalidArgumentException('User ID is required');
        }
        
        $this->validateBase64($registered, "registered_{$type}");
        $this->validateBase64($query, "query_{$type}");
    }

    protected function validateBase64(string $data, string $fieldName): void
    {
        if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data) !== 1) {
            throw new \InvalidArgumentException("Invalid base64 format for {$fieldName}");
        }
        
        // Basic size check (10MB max)
        if (strlen($data) > 13888888) {
            throw new \InvalidArgumentException("{$fieldName} exceeds maximum size (10MB)");
        }
    }

    /**
     * Convert file to base64 (helper for developers)
     */
    public static function fileToBase64(string $path): string
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found: {$path}");
        }
        
        $mime = mime_content_type($path);
        $data = base64_encode(file_get_contents($path));
        
        return "{$mime};base64,{$data}";
    }
}