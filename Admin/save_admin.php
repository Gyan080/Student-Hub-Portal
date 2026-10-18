<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data']);
    exit;
}

$adminId = strtoupper(trim($data['adminId'] ?? ''));
$name = trim($data['name'] ?? '');
$email = strtolower(trim($data['email'] ?? ''));
$role = trim($data['role'] ?? 'Department Head / Principal');
$department = trim($data['department'] ?? 'Information Technology');
$plainPassword = !empty($data['password']) ? $data['password'] : 'admin123';
$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

if (!empty($adminId) && !empty($email) && !empty($name)) {
    try {
        $stmt = $mysqli->prepare("INSERT INTO admins (admin_id, name, email, password_hash, role, department) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE name = VALUES(name), role = VALUES(role), department = VALUES(department), password_hash = VALUES(password_hash)");
        $stmt->bind_param("ssssss", $adminId, $name, $email, $passwordHash, $role, $department);
        $res = $stmt->execute();
        echo json_encode(['success' => $res]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Required fields missing']);
