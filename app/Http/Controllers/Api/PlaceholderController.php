<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceholderController extends Controller
{
    /**
     * Generate a simple placeholder image
     *
     * @param Request $request
     * @param int $width
     * @param int $height
     * @return Response
     */
    public function image(Request $request, $width = 150, $height = null)
    {
        // Set default height to width if not provided (square image)
        $height = $height ?? $width;
        
        // Validate dimensions
        $width = max(1, min(1000, (int)$width));
        $height = max(1, min(1000, (int)$height));
        
        // Create a simple SVG placeholder
        $svg = $this->generateSvgPlaceholder($width, $height);
        
        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Access-Control-Allow-Origin', '*');
    }
    
    /**
     * Generate SVG placeholder image
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    private function generateSvgPlaceholder($width, $height)
    {
        $backgroundColor = '#f0f0f0';
        $textColor = '#999999';
        $text = "{$width}x{$height}";
        
        // Calculate font size based on image size
        $fontSize = min($width, $height) / 8;
        
        return <<<SVG
<svg width="{$width}" height="{$height}" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="{$backgroundColor}"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="{$fontSize}" 
          fill="{$textColor}" text-anchor="middle" dominant-baseline="middle">{$text}</text>
</svg>
SVG;
    }
    
    /**
     * Generate a user avatar placeholder
     *
     * @param Request $request
     * @param int $size
     * @return Response
     */
    public function avatar(Request $request, $size = 150)
    {
        $size = max(50, min(500, (int)$size));
        
        $svg = $this->generateAvatarSvg($size);
        
        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Access-Control-Allow-Origin', '*');
    }
    
    /**
     * Generate SVG avatar placeholder
     *
     * @param int $size
     * @return string
     */
    private function generateAvatarSvg($size)
    {
        $backgroundColor = '#e3f2fd';
        $iconColor = '#1976d2';
        $circleRadius = $size * 0.3;
        $centerX = $size / 2;
        $centerY = $size / 2;
        $fontSize = $size / 4;
        
        return <<<SVG
<svg width="{$size}" height="{$size}" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="{$backgroundColor}" rx="{$size}"/>
    <circle cx="{$centerX}" cy="{$centerY}" r="{$circleRadius}" fill="{$iconColor}" opacity="0.7"/>
    <circle cx="{$centerX}" cy="{$centerY}" r="{$circleRadius}" fill="none" stroke="{$iconColor}" stroke-width="2"/>
    <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="{$fontSize}" 
          fill="{$iconColor}" text-anchor="middle" dominant-baseline="middle">U</text>
</svg>
SVG;
    }
}
