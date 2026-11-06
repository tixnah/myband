<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    http_response_code(403);
    exit('Connectez-vous pour télécharger les paroles.');
}

$filename = $_GET['lyricsPDF'] ?? '';
$lyricsDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'lyrics';

if (
    !is_string($filename)
    || basename($filename) !== $filename
    || !preg_match('/\A[a-zA-Z0-9._ -]+\.pdf\z/i', $filename)
) {
    http_response_code(404);
    exit;
}

$path = realpath($lyricsDirectory . DIRECTORY_SEPARATOR . $filename);
$realDirectory = realpath($lyricsDirectory);
if ($path === false || $realDirectory === false || dirname($path) !== $realDirectory || !is_file($path)) {
    http_response_code(404);
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($path) . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
