<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class FileUploadService
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'application/pdf'
    ];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'pdf'];

    /**
     * Upload evidence file with security validation
     */
    public function uploadEvidenceFile(UploadedFile $file, string $unitId): string
    {
        // Validasi keamanan file
        $this->validateFile($file);

        // Generate nama file yang aman
        $filename = $this->generateSecureFilename($file, $unitId);

        // Upload ke storage
        $this->storeFile($file, $filename);

        return $filename;
    }

    /**
     * Validasi comprehensive untuk file
     */
    private function validateFile(UploadedFile $file): void
    {
        // 1. Cek ukuran file
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
        }

        // 2. Cek MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new Exception('Tipe file tidak diizinkan. Hanya JPG, PNG, dan PDF yang diperbolehkan.');
        }

        // 3. Cek extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            throw new Exception('Extension file tidak valid.');
        }

        // 4. Cek apakah benar-benar file yang valid (bukan executable)
        if ($this->isSuspiciousFile($file)) {
            throw new Exception('File mencurigakan terdeteksi.');
        }

        // 5. Validasi image file jika tipe gambar
        if (str_starts_with($file->getMimeType(), 'image/')) {
            $this->validateImageFile($file);
        }
    }

    /**
     * Generate nama file yang aman dan unik
     */
    private function generateSecureFilename(UploadedFile $file, string $unitId): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Bersihkan nama file dari karakter berbahaya
        $safeName = Str::slug($originalName, '_');
        $safeName = substr($safeName, 0, 50); // Limit panjang nama
        
        // Format: UNITID_YYYYMMDD_RANDOMSTRING_ORIGINALNAME.ext
        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);
        
        return strtoupper($unitId) . '_' . $timestamp . '_' . $random . '_' . $safeName . '.' . $extension;
    }

    /**
     * Store file ke storage dengan path yang aman
     */
    private function storeFile(UploadedFile $file, string $filename): void
    {
        // Pastikan direktori evidence ada
        $evidencePath = storage_path('app/public/evidence');
        if (!file_exists($evidencePath)) {
            mkdir($evidencePath, 0755, true);
        }

        // Move file dengan nama yang sudah di-sanitize
        if (!$file->move($evidencePath, $filename)) {
            throw new Exception('Gagal menyimpan file.');
        }
    }

    /**
     * Cek apakah file mencurigakan
     */
    private function isSuspiciousFile(UploadedFile $file): bool
    {
        // Baca beberapa bytes pertama untuk cek signature
        $handle = fopen($file->getRealPath(), 'r');
        $firstBytes = fread($handle, 16);
        fclose($handle);

        // Cek signature file executable
        $suspiciousSignatures = [
            'MZ',        // Windows executable
            '#!/',       // Script files
            '<?php',     // PHP files
            '<script',   // HTML/JS
        ];

        foreach ($suspiciousSignatures as $signature) {
            if (str_starts_with($firstBytes, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validasi khusus untuk image file
     */
    private function validateImageFile(UploadedFile $file): void
    {
        // Coba buat resource gambar untuk validasi
        $imagePath = $file->getRealPath();
        $imageInfo = @getimagesize($imagePath);
        
        if ($imageInfo === false) {
            throw new Exception('File gambar tidak valid atau rusak.');
        }

        // Cek dimensi gambar (opsional, untuk mencegah file terlalu besar)
        [$width, $height] = $imageInfo;
        if ($width > 4000 || $height > 4000) {
            throw new Exception('Dimensi gambar terlalu besar. Maksimal 4000x4000 pixel.');
        }
    }

    /**
     * Delete file evidence
     */
    public function deleteEvidenceFile(string $filename): bool
    {
        $filePath = storage_path('app/public/evidence/' . $filename);
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return true; // File sudah tidak ada, consider as success
    }

    /**
     * Get file info for display
     */
    public function getFileInfo(string $filename): array
    {
        $filePath = storage_path('app/public/evidence/' . $filename);
        
        if (!file_exists($filePath)) {
            return [
                'exists' => false,
                'size' => 0,
                'size_formatted' => 'File tidak ditemukan',
                'mime_type' => null,
                'is_image' => false
            ];
        }

        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);
        
        return [
            'exists' => true,
            'size' => $fileSize,
            'size_formatted' => $this->formatBytes($fileSize),
            'mime_type' => $mimeType,
            'is_image' => str_starts_with($mimeType, 'image/')
        ];
    }

    /**
     * Format bytes ke human readable
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}