<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli('127.0.0.1', 'test', 'test', 'testdb', 3307);
if ($conn->connect_error) {
    $conn = new mysqli('127.0.0.1', 'root', '', 'testdb');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;color:#e84057;padding:2rem">Database connection failed: ' . htmlspecialchars($conn->connect_error) . '</div>');
}
$conn->set_charset('utf8mb4');
