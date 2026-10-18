<?php
session_start();

// 1. Session Inactivity Timeout (15 minutes = 900 seconds)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    $_SESSION = array();
    session_destroy();
    header("Location: ../../Login.html?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// 2. Authentication Protection
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Admin_login.html?error=unauthorized");
    exit();
}

// 3. Role-Based Access Control (RBAC)
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../Student/DashBoard/student_dashboard.php?error=forbidden");
    exit();
}

$adminName = htmlspecialchars($_SESSION['name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8');
$adminRole = htmlspecialchars($_SESSION['campus_role'] ?? 'Department Head', ENT_QUOTES, 'UTF-8');
$adminEmail = htmlspecialchars($_SESSION['email'] ?? 'admin@charusat.ac.in', ENT_QUOTES, 'UTF-8');
$adminDept = htmlspecialchars($_SESSION['department'] ?? 'Information Technology', ENT_QUOTES, 'UTF-8');
$adminId   = htmlspecialchars($_SESSION['username'] ?? 'ADM10025', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - CHARUSAT Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
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
      <nav class="n-links">
        <a href="../../about.html">About</a>
        <a href="../../faqs.html">FAQs</a>
        <a href="../../contact.html">Contact</a>
        <a href="../../logout.php" class="nav-btn" style="background:#B91C1C;color:#fff;border:2px solid #1A1A1A;padding:8px 16px;text-decoration:none;font-weight:700;border-radius:4px;">Logout (<?php echo $adminId; ?>)</a>
      </nav>
    </header>

    <main>
      <section id="profile" style="max-width:960px;margin:30px auto;padding:20px;">
        <header style="margin-bottom:20px;">
          <span style="display:inline-block;padding:3px 8px;font-size:0.75rem;font-weight:800;border:1.5px solid #1A1A1A;border-radius:4px;background:#D1FAE5;color:#065F46;margin-bottom:8px;">AUTHENTICATED SESSION: ADMIN</span>
          <h2>Admin Dashboard</h2>
          <p>Current administrator session and institutional profile</p>
        </header>
        <article>
          <div class="profile-grid">
            <div class="profile-box">
              <div class="box-label">Administrator Name</div>
              <div class="box-detail" id="admin-name"><?php echo $adminName; ?></div>
            </div>
            <div class="profile-box">
              <div class="box-label">Staff / Admin ID</div>
              <div class="box-detail" id="admin-id"><?php echo $adminId; ?></div>
            </div>
            <div class="profile-box">
              <div class="box-label">Campus Role</div>
              <div class="box-detail" id="admin-role"><?php echo $adminRole; ?></div>
            </div>
            <div class="profile-box">
              <div class="box-label">Official University Email</div>
              <div class="box-detail" id="admin-email"><?php echo $adminEmail; ?></div>
            </div>
            <div class="profile-box">
              <div class="box-label">Department</div>
              <div class="box-detail" id="admin-department"><?php echo $adminDept; ?></div>
            </div>
            <div class="profile-box">
              <div class="box-label">Session Status</div>
              <div class="box-detail" style="color:#059669;font-weight:700;">Active (Expires after 15m inactivity)</div>
            </div>
          </div>
        </article>
      </section>
    </main>

    <footer class="f-bar">
      <p class="f-note">Admin Dashboard · CHARUSAT Portal</p>
      <p class="f-note"><a href="../../logout.php" style="color:#B91C1C;font-weight:700;">Click here to Logout</a></p>
    </footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>