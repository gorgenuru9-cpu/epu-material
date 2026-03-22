<?php
/**
 * PHP Built-in Server Router
 * Serves static files and routes PHP requests
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve static files directly
$file = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile = __DIR__ . '/public' . $uri)) {
    if (pathinfo($publicFile, PATHINFO_EXTENSION) === 'php') {
        require $publicFile;
        return true;
    }
    
    // Serve CSS, JS, images, etc.
    $ext = pathinfo($publicFile, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($publicFile);
        return true;
    }
}

// Fall back to public/index.php for PHP routes
require __DIR__ . '/public/index.php';
