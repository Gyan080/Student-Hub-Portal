<?php
session_start();

function renderLoginError($heading, $message) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Failed - Student Portal</title>
    <link rel="stylesheet" href="studentStyle.css">
</head>
<body class="response-page">
    <div class="response-card">
        <div class="status-tag error">error</div>
        <h2 class="card-title">{$heading}</h2>
        <p class="card-msg">{$message}</p>
        <a href="Stud_login.html" class="btn-action">Go Back to Login</a>
    </div>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderLoginError('Invalid Request', 'Please submit the login form directly.');
}

$studentId = trim($_POST['student_id'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($studentId) && empty($email)) {
    renderLoginError('Missing Identifier', 'Please enter your Enrollment ID or Email.');
}

if (empty($password)) {
    renderLoginError('Missing Password', 'Please enter your password.');
}

try {
    require_once __DIR__ . '/db_connect.php';

    // Search by Enrollment ID or Email
    $identifier = !empty($studentId) ? $studentId : $email;
    $stmt = $mysqli->prepare('SELECT student_uid, name, email, course, password_hash FROM students WHERE student_uid = ? OR email = ? LIMIT 1');
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $authenticated = false;

        // Verify password using password_verify() (with plain-text fallback for legacy test data)
        if (password_verify($password, $user['password_hash'])) {
            $authenticated = true;
        } elseif ($password === $user['password_hash']) {
            $authenticated = true;
        }

        if ($authenticated) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store session attributes
            $_SESSION['username'] = $user['student_uid'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'student';
            $_SESSION['department'] = $user['course'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();

            header("Location: DashBoard/student_dashboard.php");
            exit();
        } else {
            renderLoginError('Authentication Failed', 'The password you entered is incorrect. Please try again.');
        }
    } else {
        renderLoginError('Account Not Found', 'No student record found with the provided credentials. Please register first.');
    }
} catch (mysqli_sql_exception $e) {
    renderLoginError('Database Connection Error', 'Could not connect to the database. Please ensure Apache and MySQL are running in XAMPP.');
}
