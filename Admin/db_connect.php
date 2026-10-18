<?php
$host = 'localhost';
$database = 'studenthub';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$mysqli = new mysqli($host, $username, $password, $database);
$mysqli->set_charset($charset);
