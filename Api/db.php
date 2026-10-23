<?php
header('Content-Type: application/json');
$databaseConfig = require __DIR__ . '/../config/database.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli(
    $databaseConfig['host'],
    $databaseConfig['user'],
    $databaseConfig['password'],
    $databaseConfig['database']
);
$conn->set_charset($databaseConfig['charset']);
