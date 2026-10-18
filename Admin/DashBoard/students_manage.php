<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../Admin_login.html');
    exit;
}

require '../db_connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course = isset($_GET['course']) ? trim($_GET['course']) : '';
$year = isset($_GET['study_year']) ? trim($_GET['study_year']) : '';
$msg = isset($_GET['msg']) ? trim($_GET['msg']) : '';

$searchTerm = '%' . $search . '%';

if ($course !== '' && $year !== '') {
    $sql = "SELECT student_id, student_uid, name, email, course, study_year
            FROM students
            WHERE (name LIKE ? OR email LIKE ? OR student_uid LIKE ?)
            AND course = ? AND study_year = ?
            ORDER BY student_id ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sssss', $searchTerm, $searchTerm, $searchTerm, $course, $year);
} else if ($course !== '') {
    $sql = "SELECT student_id, student_uid, name, email, course, study_year
            FROM students
            WHERE (name LIKE ? OR email LIKE ? OR student_uid LIKE ?)
            AND course = ?
            ORDER BY student_id ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $course);
} else if ($year !== '') {
    $sql = "SELECT student_id, student_uid, name, email, course, study_year
            FROM students
            WHERE (name LIKE ? OR email LIKE ? OR student_uid LIKE ?)
            AND study_year = ?
            ORDER BY student_id ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $year);
} else {
    $sql = "SELECT student_id, student_uid, name, email, course, study_year
            FROM students
            WHERE (name LIKE ? OR email LIKE ? OR student_uid LIKE ?)
            ORDER BY student_id ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$result = $stmt->get_result();

$courseQuery = "SELECT DISTINCT course FROM students WHERE course IS NOT NULL AND course != '' ORDER BY course";
$courseResult = $mysqli->query($courseQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Students - Admin Portal</title>
  <link rel="stylesheet" href="../../Fonts/local-fonts.css">
  <link rel="stylesheet" href="../adminStyle.css">
  <link rel="stylesheet" href="../admin-dark-mode.css">
  <style>
    .student-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 16px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      overflow: hidden;
    }
    .student-table th, .student-table td {
      padding: 12px 14px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.92rem;
    }
    .student-table th {
      background-color: #f8fafc;
      font-weight: 700;
      color: #334155;
    }
    .student-table tr:hover {
      background-color: #f8fafc;
    }
    .filter-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      background: #ffffff;
      padding: 16px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .filter-bar input, .filter-bar select {
      padding: 8px 12px;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      font-size: 0.9rem;
      outline: none;
    }
    .filter-bar input:focus, .filter-bar select:focus {
      border-color: #1f4e79;
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
      <nav class="n-links">
        <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
        <a href="students_manage.php" class="nav-link active">Manage Students</a>
        <a href="../../logout.php" class="nav-btn" style="background:#B91C1C;color:#fff;border:2px solid #1A1A1A;padding:8px 16px;text-decoration:none;font-weight:700;border-radius:4px;">Logout</a>
      </nav>
    </header>

    <main>
      <section style="margin-top:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
          <div>
            <h2>Student Management</h2>
            <p style="color:#64748b;font-size:0.92rem;">View, search, filter, edit, and delete student records</p>
          </div>
          <a href="student_add.php" class="btn btn-primary" style="padding:10px 20px;font-size:0.95rem;">+ Add Student</a>
        </div>

        <?php if ($msg !== ''): ?>
          <div style="padding:12px 16px;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;border-radius:6px;margin-bottom:16px;font-weight:600;">
            <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form method="GET" action="students_manage.php" class="filter-bar">
          <input type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Search name, email, or Enrollment ID..." style="min-width:220px;flex:1;">

          <select name="course">
            <option value="">All Courses</option>
            <?php if ($courseResult): ?>
              <?php while ($cRow = $courseResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($cRow['course'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $course === $cRow['course'] ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cRow['course'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endwhile; ?>
            <?php endif; ?>
          </select>

          <select name="study_year">
            <option value="">All Study Years</option>
            <option value="1st Year (Semester 1 & 2)" <?php echo $year === '1st Year (Semester 1 & 2)' ? 'selected' : ''; ?>>1st Year (Semester 1 & 2)</option>
            <option value="2nd Year (Semester 3 & 4)" <?php echo $year === '2nd Year (Semester 3 & 4)' ? 'selected' : ''; ?>>2nd Year (Semester 3 & 4)</option>
            <option value="3rd Year (Semester 5 & 6)" <?php echo $year === '3rd Year (Semester 5 & 6)' ? 'selected' : ''; ?>>3rd Year (Semester 5 & 6)</option>
            <option value="4th Year (Semester 7 & 8)" <?php echo $year === '4th Year (Semester 7 & 8)' ? 'selected' : ''; ?>>4th Year (Semester 7 & 8)</option>
          </select>

          <button type="submit" class="btn btn-primary" style="padding:8px 16px;font-size:0.9rem;">Filter</button>
          <?php if ($search !== '' || $course !== '' || $year !== ''): ?>
            <a href="students_manage.php" class="btn btn-secondary" style="padding:8px 16px;font-size:0.9rem;">Clear</a>
          <?php endif; ?>
        </form>

        <div style="overflow-x:auto;">
          <table class="student-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Enrollment ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Study Year</th>
                <th style="text-align:center;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows === 0): ?>
                <tr>
                  <td colspan="7" style="text-align:center;padding:24px;color:#64748b;font-weight:600;">No students found</td>
                </tr>
              <?php else: ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo (int)$row['student_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['student_uid'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['course'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['study_year'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td style="text-align:center;white-space:nowrap;">
                      <a href="student_edit.php?student_id=<?php echo (int)$row['student_id']; ?>" style="color:#1f4e79;font-weight:700;text-decoration:none;margin-right:12px;">Edit</a>
                      <form method="POST" action="student_delete.php" style="display:inline;" onsubmit="return confirm('Delete this student?');">
                        <input type="hidden" name="student_id" value="<?php echo (int)$row['student_id']; ?>">
                        <button type="submit" style="background:#dc2626;color:#ffffff;border:none;padding:5px 12px;border-radius:4px;cursor:pointer;font-weight:600;font-size:0.85rem;">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <footer class="f-bar" style="margin-top:auto;">
      <p class="f-note">Admin Dashboard · CHARUSAT Portal</p>
    </footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>
