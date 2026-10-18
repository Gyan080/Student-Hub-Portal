<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Admin_login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students_manage.php');
    exit;
}
require '../db_connect.php';

$id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM registrations WHERE student_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $sql = "DELETE FROM students WHERE student_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: students_manage.php?msg=Student+deleted');
    exit;
} else {
    header('Location: students_manage.php?msg=Could+not+delete+student');
    exit;
}