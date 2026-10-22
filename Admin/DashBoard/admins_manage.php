<?php
require_once __DIR__ . '/auth_check.php';
if (!is_super_admin()) {
    die("Access denied. Super admin only.");
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/audit_helper.php';

$error = '';
$message = '';

// Add one administrator after checking the required fields and login ID.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $username = strtoupper(trim($_POST['username'] ?? ''));
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = trim($_POST['role'] ?? '');

    if ($username === '' || $name === '' || $email === '' || $password === '') {
        $error = 'Username, name, email, and password are required.';
    } elseif (strlen($username) > 8) {
        $error = 'Username must be 8 characters or fewer.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif ($role !== 'admin' && $role !== 'super_admin') {
        $error = 'Choose a valid role.';
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM admins WHERE admin_id = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Username or email is already used.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO admins (admin_id, name, email, password_hash, role, department, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $department = '';
            $stmt->bind_param("ssssss", $username, $name, $email, $passwordHash, $role, $department);
            $stmt->execute();
            write_log($mysqli, $_SESSION['admin_id'], 'add_admin', 'admins', $mysqli->insert_id, 'Added admin: ' . $username);
            $message = 'Admin added successfully.';
        }
    }
}

// Switch an administrator between active and inactive.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    $id = filter_var($_POST['admin_id'] ?? '', FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        $error = 'Invalid admin ID.';
    } else {
        $stmt = $mysqli->prepare("SELECT admin_id, status FROM admins WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        if (!$admin) {
            $error = 'Admin not found.';
        } else {
            $newStatus = $admin['status'] === 'active' ? 'inactive' : 'active';
            $stmt = $mysqli->prepare("UPDATE admins SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $id);
            $stmt->execute();
            write_log($mysqli, $_SESSION['admin_id'], 'toggle_admin', 'admins', $id, 'Set ' . $admin['admin_id'] . ' to ' . $newStatus);
            $message = 'Admin status updated.';
        }
    }
}

// List administrator accounts.
$result = $mysqli->query("SELECT id, admin_id, name, email, role, status FROM admins ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Admins - Admin Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .admin-table th, .admin-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .admin-table th { background: #f8fafc; }
    body.dark-mode .admin-table th, body.dark-mode .admin-table td { color: #e5e5e5; border-color: #3a3a3a; }
    body.dark-mode .admin-table th { background: #292929; }
  </style>
</head>
<body>
  <div class="page-wrapper">
    <header class="header-bar">
      <div class="h-brand">
        <a href="../../index.html" class="b-link">
          <img src="../../Images/Screenshot 2026-07-14 215638.png" alt="Portal Logo" class="b-logo">
          <span class="b-name">Student Portal</span>
        </a>
      </div>
      <?php require __DIR__ . '/menu.php'; ?>
    </header>
    <main>
      <section style="margin-top:20px;">
        <h2>Manage Admins</h2>
        <?php if ($error !== ''): ?><p role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
        <?php if ($message !== ''): ?><p role="status"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>

        <div class="form-card" style="margin-top:16px;">
          <h3>Add Admin</h3>
          <form method="POST" action="admins_manage.php">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
              <div class="form-field"><label for="username">Username (Admin ID)</label><input id="username" name="username" maxlength="8" required></div>
              <div class="form-field"><label for="name">Name</label><input id="name" name="name" required></div>
              <div class="form-field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
              <div class="form-field"><label for="password">Password</label><input id="password" name="password" type="password" required></div>
              <div class="form-field"><label for="role">Role</label><select id="role" name="role"><option value="admin">admin</option><option value="super_admin">super_admin</option></select></div>
            </div>
            <button type="submit" class="btn btn-primary">Add Admin</button>
          </form>
        </div>

        <div style="overflow-x:auto;margin-top:20px;">
          <table class="admin-table">
            <thead><tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php while ($admin = $result->fetch_assoc()): ?>
                <tr>
                  <td><?php echo (int)$admin['id']; ?></td>
                  <td><?php echo htmlspecialchars($admin['admin_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($admin['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($admin['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($admin['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($admin['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <form method="POST" action="admins_manage.php">
                      <input type="hidden" name="action" value="toggle">
                      <input type="hidden" name="admin_id" value="<?php echo (int)$admin['id']; ?>">
                      <button type="submit" class="btn btn-secondary">Toggle active</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
    <footer class="f-bar"><p class="f-note">Admin Dashboard · CHARUSAT Portal</p></footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>
