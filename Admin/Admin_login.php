<?php
session_start();

function renderAdminLoginError($heading, $message) {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login Failed - Admin Portal</title>
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background-color:#F9FAFB;font-family:'Segoe UI',sans-serif;">
    <div style="background:#FFF;border:2px solid #1A1A1A;border-radius:8px;box-shadow:6px 6px 0px #1A1A1A;padding:30px;max-width:460px;width:90%;text-align:center;">
        <span style="display:inline-block;padding:4px 10px;font-size:0.75rem;font-weight:800;border:1.5px solid #1A1A1A;border-radius:4px;background:#FEE2E2;color:#B91C1C;margin-bottom:12px;">LOGIN ERROR</span>
        <h2 style="font-size:1.4rem;font-weight:800;color:#111827;margin-bottom:12px;">{$heading}</h2>
        <p style="font-size:0.95rem;color:#4B5563;line-height:1.6;margin-bottom:24px;">{$message}</p>
        <a href="Admin_login.html" style="display:inline-block;padding:10px 20px;background:#1F4E79;color:#FFF;text-decoration:none;font-weight:700;border:2px solid #1A1A1A;border-radius:6px;box-shadow:3px 3px 0px #1A1A1A;">Go Back to Login</a>
    </div>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderAdminLoginError('Invalid Request', 'Please submit the admin login form directly.');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$adminId = strtoupper(trim($_POST['admin_id'] ?? ''));
$fullname = trim($_POST['fullname'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($email) && empty($adminId)) {
    renderAdminLoginError('Missing Credentials', 'Please enter your Official University Email or Staff / Admin ID.');
}

if (empty($password)) {
    renderAdminLoginError('Missing Password', 'Please enter your password.');
}

try {
    require_once __DIR__ . '/db_connect.php';
    $password = trim($_POST['password'] ?? '');

    $mysqli->query("CREATE TABLE IF NOT EXISTS admins (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        admin_id VARCHAR(8) UNIQUE NOT NULL,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(100) DEFAULT 'Department Head / Principal',
        department VARCHAR(100) DEFAULT 'Information Technology'
    )");

    $checkAdmin = $mysqli->query("SELECT 1 FROM admins LIMIT 1");
    if ($checkAdmin && $checkAdmin->num_rows === 0) {
        $defaultPassHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtSeed = $mysqli->prepare("INSERT INTO admins (admin_id, name, email, password_hash, department, role) VALUES (?, ?, ?, ?, ?, ?)");
        $defaultId = 'ADM10025';
        $defaultName = 'Dr. Mrugendra Patel';
        $defaultEmail = 'admin@charusat.ac.in';
        $defaultDept = 'Information Technology';
        $defaultRole = 'Department Head / Principal';
        $stmtSeed->bind_param('ssssss', $defaultId, $defaultName, $defaultEmail, $defaultPassHash, $defaultDept, $defaultRole);
        $stmtSeed->execute();
    }

    $stmt = $mysqli->prepare('SELECT admin_id, name, email, department, role, password_hash FROM admins WHERE email = ? OR admin_id = ? LIMIT 1');
    $stmt->bind_param('ss', $email, $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        $authenticated = false;

        if (password_verify($password, $admin['password_hash'])) {
            $authenticated = true;
        } elseif ($password === $admin['password_hash']) {
            $authenticated = true;
        }

        if ($authenticated) {
            session_regenerate_id(true);

            $_SESSION['username'] = $admin['admin_id'];
            $_SESSION['name'] = $admin['name'];
            $_SESSION['email'] = $admin['email'];
            $_SESSION['role'] = 'admin';
            $_SESSION['department'] = $admin['department'];
            $_SESSION['campus_role'] = $admin['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();

            header("Location: DashBoard/admin_dashboard.php");
            exit();
        } else {
            renderAdminLoginError('Authentication Failed', 'The password entered is incorrect for this administrator account.');
        }
    } else {
        renderAdminLoginError('Admin Not Found', 'No administrator profile was found with that email or ID. Default demo account: admin@charusat.ac.in (pass: admin123).');
    }
} catch (mysqli_sql_exception $e) {
    renderAdminLoginError('Database Error', 'Could not connect to the database. Please ensure Apache and MySQL are running in XAMPP.');
}