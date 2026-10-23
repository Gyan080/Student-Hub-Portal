<?php
$databaseConfig = require __DIR__ . '/../config/database.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli(
    $databaseConfig['host'],
    $databaseConfig['user'],
    $databaseConfig['password'],
    $databaseConfig['database']
);
$mysqli->set_charset($databaseConfig['charset']);
