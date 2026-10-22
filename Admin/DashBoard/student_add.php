<?php
require_once __DIR__ . '/auth_check.php';
require '../db_connect.php';
require_once __DIR__ . '/audit_helper.php';

$error = '';
$uid = '';
$name = '';
$email = '';
$dob = '';
$gender = 'male';
$phone = '';
$address = '';
$course = '';
$year = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = trim($_POST['student_uid'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year = trim($_POST['study_year'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($uid === '' || $name === '' || $email === '' || $dob === '' || $gender === '' || $phone === '' || $address === '' || $course === '' || $year === '' || $password === '') {
        $error = 'All fields are required.';
    } else if (substr($email, -16) !== '@charusat.edu.in') {
        $error = 'Email must end with @charusat.edu.in.';
    } else if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Phone number must be exactly 10 digits.';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    }

    if ($error === '') {
        $checkSql = "SELECT student_id FROM students WHERE student_uid = ? OR email = ? LIMIT 1";
        $checkStmt = $mysqli->prepare($checkSql);
        $checkStmt->bind_param('ss', $uid, $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $error = 'A student with this Enrollment ID or Email already exists.';
        }
    }
    
    if ($error === '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO students (student_uid, name, email, password_hash, dob, gender, phone, address, course, study_year)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('ssssssssss', $uid, $name, $email, $hash, $dob, $gender, $phone, $address, $course, $year);

        if ($stmt->execute()) {
            write_log($mysqli, $_SESSION['admin_id'], 'add_student', 'students', $mysqli->insert_id, 'Added student: ' . $uid);
            header('Location: students_manage.php?msg=Student+added');
            exit;
        } else {
            $error = 'Could not save student. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Add Student - Admin Portal</title>
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
      <?php require __DIR__ . '/menu.php'; ?>
    </header>

    <main>
      <section class="form-card" style="margin-top:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
          <h2>Add New Student</h2>
          <a href="students_manage.php" class="btn btn-secondary" style="padding:8px 16px;font-size:0.9rem;">Back to List</a>
        </div>

        <?php if ($error !== ''): ?>
          <div style="padding:12px 16px;background:#fee2e2;color:#991b1b;border:1px solid #f87171;border-radius:6px;margin-bottom:16px;font-weight:600;">
            <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="student_add.php">
          <div class="form-grid">
            <div class="form-field">
              <label for="student_uid" style="font-weight:700;font-size:0.9rem;">Enrollment ID (student_uid)</label>
              <input type="text" id="student_uid" name="student_uid" value="<?php echo htmlspecialchars($uid, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. 25dcs080" required>
            </div>

            <div class="form-field">
              <label for="name" style="font-weight:700;font-size:0.9rem;">Full Name</label>
              <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. Rahul Patel" required>
            </div>

            <div class="form-field">
              <label for="email" style="font-weight:700;font-size:0.9rem;">University Email</label>
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. 25dcs080@charusat.edu.in" required>
            </div>

            <div class="form-field">
              <label for="dob" style="font-weight:700;font-size:0.9rem;">Date of Birth</label>
              <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($dob, ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div class="form-field">
              <label style="font-weight:700;font-size:0.9rem;">Gender</label>
              <div style="display:flex;gap:16px;padding-top:8px;">
                <label><input type="radio" name="gender" value="male" <?php echo $gender === 'male' ? 'checked' : ''; ?>> Male</label>
                <label><input type="radio" name="gender" value="female" <?php echo $gender === 'female' ? 'checked' : ''; ?>> Female</label>
                <label><input type="radio" name="gender" value="other" <?php echo $gender === 'other' ? 'checked' : ''; ?>> Other</label>
              </div>
            </div>

            <div class="form-field">
              <label for="phone" style="font-weight:700;font-size:0.9rem;">Phone (10 digits)</label>
              <input type="tel" id="phone" name="phone" maxlength="10" value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. 9876543210" required>
            </div>

            <div class="form-field">
              <label for="course" style="font-weight:700;font-size:0.9rem;">Course</label>
              <select id="course" name="course" required>
                <option value="" disabled <?php echo $course === '' ? 'selected' : ''; ?>>Select Course</option>
                <option value="Information Technology" <?php echo $course === 'Information Technology' ? 'selected' : ''; ?>>Information Technology</option>
                <option value="Computer Engineering" <?php echo $course === 'Computer Engineering' ? 'selected' : ''; ?>>Computer Engineering</option>
                <option value="Computer Science Engineering" <?php echo $course === 'Computer Science Engineering' ? 'selected' : ''; ?>>Computer Science Engineering</option>
              </select>
            </div>

            <div class="form-field">
              <label for="study_year" style="font-weight:700;font-size:0.9rem;">Study Year</label>
              <select id="study_year" name="study_year" required>
                <option value="" disabled <?php echo $year === '' ? 'selected' : ''; ?>>Select Year</option>
                <option value="1st Year (Semester 1 & 2)" <?php echo $year === '1st Year (Semester 1 & 2)' ? 'selected' : ''; ?>>1st Year (Semester 1 & 2)</option>
                <option value="2nd Year (Semester 3 & 4)" <?php echo $year === '2nd Year (Semester 3 & 4)' ? 'selected' : ''; ?>>2nd Year (Semester 3 & 4)</option>
                <option value="3rd Year (Semester 5 & 6)" <?php echo $year === '3rd Year (Semester 5 & 6)' ? 'selected' : ''; ?>>3rd Year (Semester 5 & 6)</option>
                <option value="4th Year (Semester 7 & 8)" <?php echo $year === '4th Year (Semester 7 & 8)' ? 'selected' : ''; ?>>4th Year (Semester 7 & 8)</option>
              </select>
            </div>

            <div class="form-field" style="grid-column: span 2;">
              <label for="address" style="font-weight:700;font-size:0.9rem;">Address</label>
              <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Residential Address" required>
            </div>

            <div class="form-field" style="grid-column: span 2;">
              <label for="password" style="font-weight:700;font-size:0.9rem;">Password</label>
              <input type="password" id="password" name="password" placeholder="Password (min 6 characters)" required>
            </div>
          </div>

          <div style="display:flex;gap:14px;margin-top:20px;">
            <button type="submit" class="btn btn-primary">Save Student</button>
            <a href="students_manage.php" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </section>
    </main>

    <footer class="f-bar" style="margin-top:auto;">
      <p class="f-note">Admin Dashboard · CHARUSAT Portal</p>
    </footer>
  </div>
  <script src="../../dark-mode.js"></script>
</body>
</html>