<?php
session_start();

// 1. Session Inactivity Timeout (15 minutes = 900 seconds)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    session_destroy();
    header("Location: ../../Login.html?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// 2. Authentication Protection
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Stud_login.html?error=unauthorized");
    exit();
}

// 3. Role-Based Access Control (RBAC)
if (($_SESSION['role'] ?? null) !== 'student') {
    if (($_SESSION['role'] ?? null) === 'admin') {
        header("Location: ../../Admin/DashBoard/admin_dashboard.php?error=forbidden");
    } else {
        header("Location: ../Stud_login.html?error=unauthorized");
    }
    exit();
}

$studentName = htmlspecialchars($_SESSION['name'] ?? 'Student', ENT_QUOTES, 'UTF-8');
$studentId   = htmlspecialchars($_SESSION['username'] ?? '25DCS080', ENT_QUOTES, 'UTF-8');
$studentDept = htmlspecialchars($_SESSION['department'] ?? 'Information Technology', ENT_QUOTES, 'UTF-8');

// Compute avatar initials
$nameParts = explode(' ', trim($studentName));
$initials = strtoupper(substr($nameParts[0], 0, 1));
if (count($nameParts) > 1) {
    $initials .= strtoupper(substr(end($nameParts), 0, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Dashboard - CHARUSAT Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../studentStyle.css">
  <link rel="stylesheet" href="../student_darkmode.css">
</head>
<body>
  <div class="page-wrapper">
    <header class="header-bar">
      <div class="h-brand">
        <a href="../../index.html" class="b-link">
          <img src="../../Images/Screenshot 2026-07-14 215638.png" alt="University Logo" class="b-logo">
          <span class="b-name">STUDENT PORTAL</span>
        </a>
      </div>
      <nav class="n-links">
        <a href="../../index.html" class="nav-link">Home</a>
        <a href="../../about.html" class="nav-link">About</a>
        <a href="../../faqs.html" class="nav-link">FAQs</a>
        <a href="../../contact.html" class="nav-link">Contact</a>
        <a href="../../logout.php" class="nav-btn logout-btn">Logout (<?php echo $studentId; ?>)</a>
      </nav>
    </header>

    <div class="d-layout">
      <aside class="d-sidebar">
        <div class="user-card">
          <div class="u-avatar" id="avatar-initials"><?php echo $initials; ?></div>
          <div class="u-meta">
            <strong class="user-name" id="sidebar-user-name"><?php echo $studentName; ?></strong>
            <span class="user-role" id="sidebar-user-id"><?php echo $studentId; ?></span>
          </div>
        </div>

        <nav class="s-nav">
          <h2 class="s-head">PORTAL MENU</h2>
          <ul class="s-menu">
            <li><a href="student_dashboard.php" class="s-link active"><span></span> Overview Dashboard</a></li>
            <li><a href="../Profile/profile.html" class="s-link"><span></span> Student Profile</a></li>
            <li><a href="../Exam_Result/exam_result.html" class="s-link"><span></span> Exam Results</a></li>
            <li><a href="../Event/event.html" class="s-link"><span></span> Campus Events</a></li>
          </ul>
        </nav>

        <div class="sidebar-notice">
          <span class="notice-mini-tag">ACTIVE SESSION</span>
          <p>Logged in as: <strong>Student</strong><br>Session active since <?php echo date("h:i A"); ?></p>
        </div>
      </aside>

      <main class="d-main">
        <section class="w-banner">
          <div class="b-text">
            <div class="c-head-tag">ACADEMIC HUB</div>
            <h1 class="b-title">Welcome back, <span id="student-name"><?php echo $studentName; ?></span>!</h1>
            <p class="b-sub">Enrolled in <strong id="student-department"><?php echo $studentDept; ?></strong> | Enrollment ID: <strong id="student-id"><?php echo $studentId; ?></strong></p>
          </div>
          <div class="b-acts">
            <a href="../Exam_Result/exam_result.html" class="btn btn-secondary">View Marksheet</a>
          </div>
        </section>

        <section class="m-grid">
          <article class="m-card">
            <div class="metric-top">
              <span class="metric-title">OVERALL ATTENDANCE</span>
              <span class="kpi-badge green-badge">Eligible (75%)</span>
            </div>
            <div class="metric-main">
              <span class="metric-number">88.4%</span>
              <span class="metric-sub">145 / 164 Lectures</span>
            </div>
            <div class="progress-bar-wrap">
              <div class="progress-bar-fill green-fill" style="width: 88.4%;"></div>
            </div>
          </article>

          <article class="m-card">
            <div class="metric-top">
              <span class="metric-title">CUMULATIVE CGPA</span>
              <span class="kpi-badge green-badge">Distinction</span>
            </div>
            <div class="metric-main">
              <span class="metric-number">8.65</span>
              <span class="metric-sub">Out of 10.00 Scale</span>
            </div>
            <p class="metric-hint">Ranked in top 10% of department</p>
          </article>

          <article class="m-card">
            <div class="metric-top">
              <span class="metric-title">SEMESTER FEE STATUS</span>
              <span class="kpi-badge green-badge">Clear</span>
            </div>
            <div class="metric-main">
              <span class="metric-number">₹ 0.00</span>
              <span class="metric-sub">Total Due Outstanding</span>
            </div>
          </article>

          <article class="m-card">
            <div class="metric-top">
              <span class="metric-title">UPCOMING EXAM</span>
              <span class="kpi-badge gold-badge">10 Oct</span>
            </div>
            <div class="metric-main">
              <span class="metric-number">ITUE203</span>
              <span class="metric-sub">Web Dev Frameworks</span>
            </div>
          </article>
        </section>

        <div class="courses-notices-grid">
          <section class="d-panel">
            <div class="p-head-bar">
              <h2>Current Semester Courses</h2>
              <span class="count-tag">5 Subjects</span>
            </div>
            <div class="table-container">
              <table class="b-table">
                <thead>
                  <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Subject Title</th>
                    <th scope="col">Credits</th>
                    <th scope="col">Attendance</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>ITUE203</strong></td>
                    <td>Web Development Frameworks</td>
                    <td>4</td>
                    <td><span class="tag tag-green">92%</span></td>
                  </tr>
                  <tr>
                    <td><strong>ITUC201</strong></td>
                    <td>Fundamental of Computer Networking</td>
                    <td>4</td>
                    <td><span class="tag tag-green">86%</span></td>
                  </tr>
                  <tr>
                    <td><strong>CSUC201</strong></td>
                    <td>Fundamental of Data Structure & Algorithms</td>
                    <td>4</td>
                    <td><span class="tag tag-green">84%</span></td>
                  </tr>
                  <tr>
                    <td><strong>CEUE203</strong></td>
                    <td>Object Oriented Programming with JAVA</td>
                    <td>5</td>
                    <td><span class="tag tag-green">90%</span></td>
                  </tr>
                  <tr>
                    <td><strong>HSUE201</strong></td>
                    <td>Professional Communication</td>
                    <td>2</td>
                    <td><span class="tag tag-green">90%</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <section class="d-panel">
            <div class="p-head-bar">
              <h2>Campus Announcements</h2>
              <span class="count-tag gold-tag">Latest</span>
            </div>
            <div class="notice-items-stack">
              <article class="notice-item">
                <span class="notice-date">25 SEP 2026</span>
                <h3>Tech Fest 2026 Registrations Open</h3>
                <p>Hackathon, Web Design, and Coding sprints registrations are live. Visit the Events portal to register your team.</p>
              </article>

              <article class="notice-item">
                <span class="notice-date">18 SEP 2026</span>
                <h3>Mid-Semester Timetable Published</h3>
                <p>Check the calendar section for hall allocations and time slots for all Semester 3 theory exams.</p>
              </article>

              <article class="notice-item">
                <span class="notice-date">10 SEP 2026</span>
                <h3>Library Digital Portal Access</h3>
                <p>IEEE and Springer digital journal access credentials updated for all registered IT department students.</p>
              </article>
            </div>
          </section>
        </div>
      </main>
    </div>

    <footer class="f-bar">
      <div class="f-left">
        <p class="f-title"><strong>CHARUSAT Student Hub Portal</strong></p>
        <p class="f-note">2026 Department of Information Technology. All Rights Reserved.</p>
      </div>
      <div class="f-right">
        <a href="../../index.html">Home</a>
        <a href="../../about.html">About</a>
        <a href="../../faqs.html">FAQs</a>
        <a href="../../contact.html">Support</a>
        <a href="../../logout.php">Logout</a>
      </div>
    </footer>
  </div>

  <script src="menu.js"></script>
  <script src="../../dark-mode.js"></script>
</body>
</html>