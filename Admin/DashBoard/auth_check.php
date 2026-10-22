<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Admin_login.php");
    exit;
}

if (($_SESSION['role'] ?? null) !== 'admin') {
    if (($_SESSION['role'] ?? null) === 'student') {
        header("Location: ../../Student/DashBoard/student_dashboard.php?error=forbidden");
    } else {
        header("Location: ../Admin_login.php");
    }
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Admin_login.php");
    exit;
}

if (!isset($_SESSION['last_activity']) || time() - $_SESSION['last_activity'] > 900) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    session_destroy();
    header("Location: ../Admin_login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();

function is_super_admin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin';
}
