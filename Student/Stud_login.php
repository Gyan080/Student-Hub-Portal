<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Stud_login.html");
    exit;
}

$studentId     = trim($_POST['student_id'] ?? '');
$typedPassword = $_POST['password'] ?? '';

if (empty($studentId)) {
    $error = "Please enter your Enrollment ID or Email.";
    include 'login_error.php';
    exit;
}

if (empty($typedPassword)) {
    $error = "Please enter your password.";
    include 'login_error.php';
    exit;
}

require_once __DIR__ . '/db_connect.php';

$stmt = $mysqli->prepare('SELECT student_id, student_uid, name, email, course, password_hash FROM students WHERE student_uid = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $studentId, $studentId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $error = "No student account found with that ID or email. Please register first.";
    include 'login_error.php';
    exit;
}

if (!password_verify($typedPassword, $user['password_hash'])) {
    $error = "Incorrect password. Please try again.";
    include 'login_error.php';
    exit;
}

session_regenerate_id(true);
$_SESSION['student_id']    = (int)$user['student_id'];
$_SESSION['username']      = $user['student_uid'];
$_SESSION['name']          = $user['name'];
$_SESSION['email']         = $user['email'];
$_SESSION['role']          = 'student';
$_SESSION['department']    = $user['course'];
$_SESSION['logged_in']     = true;
$_SESSION['last_activity'] = time();

header("Location: DashBoard/student_dashboard.php");
exit;