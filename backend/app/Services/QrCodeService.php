<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique tutor ID code
     */
    public function generateTutorIdCode(): string
    {
        $year = date('Y');
        $randomCode = strtoupper(Str::random(6));
        
        return "PRIEDU-{$year}-{$randomCode}";
    }

    /**
     * Generate QR code as base64 data URL
     */
    public function generateQrCode(string $url, int $size = 300): string
    {
        $qrCode = QrCode::format('png')
            ->size($size)
            ->errorCorrection('H') // High error correction (30%)
            ->margin(2)
            ->generate($url);

        return 'data:image/png;base64,' . base64_encode($qrCode);
    }

    /**
     * Generate QR code as SVG
     */
    public function generateQrCodeSvg(string $url): string
    {
        return QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->margin(2)
            ->generate($url);
    }

    /**
     * Get public profile URL for tutor
     */
    public function getPublicProfileUrl(string $tutorIdCode): string
    {
        $baseUrl = config('app.frontend_url', 'https://priedu.com');
        return "{$baseUrl}/tutor/{$tutorIdCode}";
    }

    /**
     * Validate tutor ID code format
     */
    public function isValidTutorIdCode(string $code): bool
    {
        // Format: PRIEDU-YYYY-XXXXXX
        return preg_match('/^PRIEDU-\d{4}-[A-Z0-9]{6}$/', $code) === 1;
    }
}
