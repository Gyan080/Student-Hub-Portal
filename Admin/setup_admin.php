<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This setup script can only be run from the command line.\n");
}

require_once __DIR__ . '/db_connect.php';

$readPrompt = static function (string $message): string {
    if (function_exists('readline')) {
        $input = readline($message);
    } else {
        fwrite(STDOUT, $message);
        $input = fgets(STDIN);
    }

    return $input === false ? '' : $input;
};

$lockResult = $mysqli->query("SELECT GET_LOCK('studenthub_admin_setup', 10)")->fetch_row();
if (!$lockResult || (int)$lockResult[0] !== 1) {
    throw new RuntimeException('Could not acquire the administrator setup lock.');
}

try {
    $adminCount = (int)$mysqli->query('SELECT COUNT(*) FROM admins')->fetch_row()[0];
    if ($adminCount > 0) {
        throw new RuntimeException('Setup is disabled because an administrator already exists.');
    }

    $readValue = static function (string $environmentVariable, string $message) use ($readPrompt): string {
        $value = getenv($environmentVariable);
        if ($value !== false && $value !== '') {
            return trim($value);
        }

        return trim($readPrompt($message));
    };

    $adminId = strtoupper($readValue('ADMIN_ID', 'Admin ID (e.g. ADM10025): '));
    $name = $readValue('ADMIN_NAME', 'Full name: ');
    $email = strtolower($readValue('ADMIN_EMAIL', 'Email: '));
    $department = $readValue('ADMIN_DEPARTMENT', 'Department [Information Technology]: ');
    if ($department === '') {
        $department = 'Information Technology';
    }

    $password = getenv('ADMIN_SETUP_PASSWORD');
    if ($password === false || $password === '') {
        $password = $readPrompt('Password (minimum 8 characters): ');
    }

    if ($adminId === '' || $name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('A valid admin ID, name, and email are required.');
    }
    if (strlen($password) < 8) {
        throw new InvalidArgumentException('Password must be at least 8 characters.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'super_admin';
    $stmt = $mysqli->prepare(
        'INSERT INTO admins (admin_id, name, email, password_hash, role, department, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $status = 'active';
    $stmt->bind_param('sssssss', $adminId, $name, $email, $passwordHash, $role, $department, $status);
    $stmt->execute();

    echo "Super administrator created successfully.\n";
} finally {
    $mysqli->query("SELECT RELEASE_LOCK('studenthub_admin_setup')");
}
