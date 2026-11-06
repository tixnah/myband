<?php
function dbconnect(): PDO
{
    $host = getenv('MYBAND_DB_HOST') ?: 'localhost';
    $database = getenv('MYBAND_DB_NAME') ?: 'myband';
    $username = getenv('MYBAND_DB_USER') ?: 'root';
    $password = getenv('MYBAND_DB_PASSWORD');
    $password = $password === false ? '' : $password;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $database);

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
