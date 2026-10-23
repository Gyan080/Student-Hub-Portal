<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true
    || ($_SESSION['role'] ?? null) !== 'admin'
    || ($_SESSION['admin_role'] ?? null) !== 'super_admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden']);
    exit;
}

$respond = static function (bool $success, string $error = ''): void {
    echo json_encode($success ? ['success' => true] : ['success' => false, 'error' => $error]);
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $respond(false, 'Method not allowed');
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    $respond(false, 'No data');
    exit;
}

$adminFields = [
    'adminId' => $data['adminId'] ?? null,
    'name' => $data['name'] ?? null,
    'email' => $data['email'] ?? null,
    'role' => $data['role'] ?? 'Department Head / Principal',
    'department' => $data['department'] ?? 'Information Technology',
    'password' => $data['password'] ?? null,
];

foreach ($adminFields as $value) {
    if (!is_string($value)) {
        http_response_code(400);
        $respond(false, 'Required fields missing');
        exit;
    }
}

$adminId = strtoupper(trim($adminFields['adminId']));
$name = trim($adminFields['name']);
$email = strtolower(trim($adminFields['email']));
$role = trim($adminFields['role']);
$department = trim($adminFields['department']);
$plainPassword = $adminFields['password'];

if ($adminId === '' || $email === '' || $name === '') {
    http_response_code(400);
    $respond(false, 'Required fields missing');
    exit;
}

if (strlen($plainPassword) < 8) {
    http_response_code(400);
    $respond(false, 'Password must be at least 8 characters');
    exit;
}

try {
    require_once __DIR__ . '/db_connect.php';
    $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare(
        'INSERT INTO admins (admin_id, name, email, password_hash, role, department) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('ssssss', $adminId, $name, $email, $passwordHash, $role, $department);
    $stmt->execute();
    $respond(true);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() === 1062) {
        http_response_code(409);
        $respond(false, 'An administrator with this ID or email already exists');
        exit;
    }

    error_log('Admin creation failed: ' . $e->getMessage());
    http_response_code(500);
    $respond(false, 'Could not save administrator');
}
