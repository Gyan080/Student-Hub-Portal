<?php
require_once __DIR__ . '/auth_check.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: students_manage.php');
    exit;
}
require '../db_connect.php';
require_once __DIR__ . '/audit_helper.php';

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

    write_log($mysqli, $_SESSION['admin_id'], 'delete_student', 'students', $id, 'Deleted student');
    header('Location: students_manage.php?msg=Student+deleted');
    exit;
} else {
    header('Location: students_manage.php?msg=Could+not+delete+student');
    exit;
}