<?php
$filename = $_GET['logo'] ?? '';
$logosDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'logos';

if (
    !is_string($filename)
    || basename($filename) !== $filename
    || !preg_match('/\A[a-zA-Z0-9._-]+\.(?:jpe?g|png)\z/i', $filename)
) {
    http_response_code(404);
    exit;
}

$path = realpath($logosDirectory . DIRECTORY_SEPARATOR . $filename);
$realDirectory = realpath($logosDirectory);
if ($path === false || $realDirectory === false || dirname($path) !== $realDirectory || !is_file($path)) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . (mime_content_type($path) ?: 'application/octet-stream'));
header('X-Content-Type-Options: nosniff');
readfile($path);
