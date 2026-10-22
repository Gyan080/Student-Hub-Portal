<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../db_connect.php';

$adminName = htmlspecialchars($_SESSION['name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8');
$adminRole = htmlspecialchars($_SESSION['campus_role'] ?? $_SESSION['admin_role'], ENT_QUOTES, 'UTF-8');
$adminEmail = htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8');
$adminDept = htmlspecialchars($_SESSION['department'] ?? '', ENT_QUOTES, 'UTF-8');
$adminUsername = htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8');

// Load the dashboard totals.
$studentCount = (int)$mysqli->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$eventCount = (int)$mysqli->query("SELECT COUNT(*) FROM events")->fetch_row()[0];
$openEventCount = (int)$mysqli->query("SELECT COUNT(*) FROM events WHERE status = 'open'")->fetch_row()[0];
$registrationCount = (int)$mysqli->query("SELECT COUNT(*) FROM registrations")->fetch_row()[0];
$adminCount = (int)$mysqli->query("SELECT COUNT(*) FROM admins")->fetch_row()[0];

// Load the latest registrations with their student and event names.
$stmt = $mysqli->prepare("SELECT r.registration_date, s.name AS student_name, s.student_uid, e.title AS event_title
                          FROM registrations r
                          JOIN students s ON r.student_id = s.student_id
                          JOIN events e ON r.event_id = e.event_id
                          ORDER BY r.registration_date DESC
                          LIMIT 5");
$stmt->execute();
$registrations = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - CHARUSAT Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .dashboard-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
      background: #fff;
    }
    .dashboard-table th, .dashboard-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }
    .dashboard-table th {
      background: #f8fafc;
    }
    body.dark-mode .dashboard-table {
      background: #1e1e1e;
    }
    body.dark-mode .dashboard-table th,
    body.dark-mode .dashboard-table td {
      color: #e5e5e5;
      border-color: #3a3a3a;
    }
    body.dark-mode .dashboard-table th {
      background: #292929;
    }
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
        <header style="margin-bottom:20px;">
          <h2>Admin Dashboard</h2>
          <p>Current administrator session and portal activity</p>
        </header>

        <div class="profile-grid">
          <div class="profile-box"><div class="box-label">Total Students</div><div class="box-detail"><?php echo $studentCount; ?></div></div>
          <div class="profile-box"><div class="box-label">Total Events</div><div class="box-detail"><?php echo $eventCount; ?></div></div>
          <div class="profile-box"><div class="box-label">Open Events</div><div class="box-detail"><?php echo $openEventCount; ?></div></div>
          <div class="profile-box"><div class="box-label">Total Registrations</div><div class="box-detail"><?php echo $registrationCount; ?></div></div>
          <div class="profile-box"><div class="box-label">Total Admins</div><div class="box-detail"><?php echo $adminCount; ?></div></div>
        </div>

        <article class="profile-box" style="margin-top:20px;">
          <h3>Latest Registrations</h3>
          <?php if ($registrations->num_rows === 0): ?>
            <p style="margin-top:12px;">No registrations yet</p>
          <?php else: ?>
            <div style="overflow-x:auto;">
              <table class="dashboard-table">
                <thead><tr><th>Date</th><th>Student</th><th>Enrollment ID</th><th>Event</th></tr></thead>
                <tbody>
                  <?php while ($registration = $registrations->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($registration['registration_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($registration['student_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($registration['student_uid'], ENT_QUOTES, 'UTF-8'); ?></td>
                      <td><?php echo htmlspecialchars($registration['event_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </article>

        <article class="profile-box" style="margin-top:20px;">
          <h3>Administrator Profile</h3>
          <div class="profile-grid">
            <div><div class="box-label">Name</div><div class="box-detail"><?php echo $adminName; ?></div></div>
            <div><div class="box-label">Admin ID</div><div class="box-detail"><?php echo $adminUsername; ?></div></div>
            <div><div class="box-label">Role</div><div class="box-detail"><?php echo $adminRole; ?></div></div>
            <div><div class="box-label">Email</div><div class="box-detail"><?php echo $adminEmail; ?></div></div>
            <div><div class="box-label">Department</div><div class="box-detail"><?php echo $adminDept; ?></div></div>
          </div>
        </article>
      </section>
    </main>

    <footer class="f-bar">
      <p class="f-note">Admin Dashboard · CHARUSAT Portal</p>
    </footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>
