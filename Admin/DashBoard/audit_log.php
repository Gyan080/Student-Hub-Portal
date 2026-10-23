<?php
require_once __DIR__ . '/auth_check.php';
if (!is_super_admin()) {
    die("Access denied. Super admin only.");
}

require_once __DIR__ . '/../db_connect.php';

// Load the latest audit entries with their admin or student actor.
$stmt = $mysqli->prepare("SELECT l.created_at, COALESCE(a.admin_id, 'Student') AS username, l.action, l.target_table, l.target_id, l.details
                          FROM audit_logs l
                          LEFT JOIN admins a ON l.admin_id = a.id
                          ORDER BY l.created_at DESC
                          LIMIT 100");
$stmt->execute();
$logs = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Audit Log - Admin Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .audit-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    .audit-table th, .audit-table td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    .audit-table th { background: #f8fafc; }
    body.dark-mode .audit-table th, body.dark-mode .audit-table td { color: #e5e5e5; border-color: #3a3a3a; }
    body.dark-mode .audit-table th { background: #292929; }
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
        <h2>Audit Log</h2>
        <div style="overflow-x:auto;">
          <table class="audit-table">
            <thead><tr><th>Time</th><th>Actor</th><th>Action</th><th>Target table</th><th>Target ID</th><th>Details</th></tr></thead>
            <tbody>
              <?php if ($logs->num_rows === 0): ?>
                <tr><td colspan="6">No audit entries yet</td></tr>
              <?php else: ?>
                <?php while ($log = $logs->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($log['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($log['target_table'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)($log['target_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($log['details'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
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
