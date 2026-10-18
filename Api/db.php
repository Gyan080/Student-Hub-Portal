<?php
header('Content-Type: application/json');

$host = 'localhost';
$database = 'studenthub';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $username, $password, $database);
$conn->set_charset($charset);
