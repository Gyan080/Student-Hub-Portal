<?php
header('Content-Type: text/html; charset=utf-8');

function renderPage(string $title, string $heading, string $message, string $type = 'error'): void
{
    $buttonText = $type === 'success' ? 'Proceed to Student Login' : 'Go Back to Form';
    $buttonLink = $type === 'success' ? 'Stud_login.html' : 'regis_stud.html';
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeHeading = htmlspecialchars($heading, ENT_QUOTES, 'UTF-8');

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$safeTitle} - Student Registration</title>
    <link rel="stylesheet" href="studentStyle.css">
</head>
<body class="response-page">
    <div class="response-card">
        <div class="status-tag {$type}">{$type}</div>
        <h2 class="card-title">{$safeHeading}</h2>
        <p class="card-msg">{$message}</p>
        <a href="{$buttonLink}" class="btn-action">{$buttonText}</a>
    </div>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renderPage('Method Not Allowed', 'Invalid Request', 'Please submit the student registration form directly.');
}

$firstname = trim($_POST['f_name'] ?? '');
$middlename = trim($_POST['m_name'] ?? '');
$lastname = trim($_POST['L_name'] ?? '');
$collegeId = strtolower(trim($_POST['college_id'] ?? ''));
$dob = trim($_POST['dob'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$course = trim($_POST['course'] ?? '');
$year = trim($_POST['year'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$namePattern = '/^[A-Za-z]{2,50}$/';
$phonePattern = '/^[6-9]\d{9}$/';
$courseBranches = [
    'Information Technology' => 'dit',
    'Computer Engineering' => 'dce',
    'Computer Science Engineering' => 'dcs',
];
$studyYears = [
    '1st Year (Semester 1 & 2)',
    '2nd Year (Semester 3 & 4)',
    '3rd Year (Semester 5 & 6)',
    '4th Year (Semester 7 & 8)',
];

foreach ([
    'First Name' => $firstname,
    'Middle Name' => $middlename,
    'Last Name' => $lastname,
] as $label => $name) {
    if (!preg_match($namePattern, $name)) {
        renderPage('Validation Error', "Invalid {$label}", "{$label} must contain only letters (2-50 characters).");
    }
}

if (!isset($courseBranches[$course])) {
    renderPage('Validation Error', 'Invalid Course', 'Please select Information Technology, Computer Engineering, or Computer Science Engineering.');
}

$branchCode = $courseBranches[$course];
$enrollmentPattern = '/^\d{2}(dit|dce|dcs)\d{3}$/';

if (!preg_match($enrollmentPattern, $collegeId, $enrollmentMatches)) {
    renderPage('Validation Error', 'Invalid Enrollment ID Format', 'Enrollment ID must contain 2 digits, a valid branch code (dit, dce, or dcs), then 3 digits (e.g. 25dcs080).');
}

if ($enrollmentMatches[1] !== $branchCode) {
    renderPage('Validation Error', 'Course and Enrollment ID Do Not Match', "The selected course is {$course}, but Enrollment ID {$collegeId} uses the {$enrollmentMatches[1]} branch code. Select the matching course or correct the Enrollment ID.");
}

if (!preg_match('/^(\d{2}(dit|dce|dcs)\d{3})@charusat\.edu\.in$/', $email, $emailMatches)) {
    renderPage('Validation Error', 'Invalid University Email Format', "Use your Enrollment ID followed by @charusat.edu.in (e.g. {$collegeId}@charusat.edu.in).");
}

if ($emailMatches[2] !== $branchCode) {
    renderPage('Validation Error', 'Course and Email Do Not Match', "The selected course is {$course}, but the email uses the {$emailMatches[2]} branch code. Use the email address for your selected course.");
}

if ($emailMatches[1] !== $collegeId) {
    renderPage('Validation Error', 'Email Mismatch', 'The university email prefix must match the Enrollment ID.');
}

$date = DateTimeImmutable::createFromFormat('!Y-m-d', $dob);
if ($date === false || $date->format('Y-m-d') !== $dob) {
    renderPage('Validation Error', 'Invalid Date of Birth', 'Please select a valid Date of Birth.');
}

if (!in_array($gender, ['male', 'female', 'other'], true)) {
    renderPage('Validation Error', 'Invalid Gender', 'Please select a valid gender.');
}

if (!preg_match($phonePattern, $phone)) {
    renderPage('Validation Error', 'Invalid Phone Number', 'Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.');
}

if ($address === '') {
    renderPage('Validation Error', 'Address Required', 'Please enter your residential address.');
}

if (!in_array($year, $studyYears, true)) {
    renderPage('Validation Error', 'Course / Year Required', 'Please select a valid Course Program and Year of Study.');
}

if (!preg_match('/^.{6,}$/us', $password)) {
    renderPage('Validation Error', 'Invalid Password', 'Password must be at least 6 characters long.');
}

if ($password !== $confirmPassword) {
    renderPage('Validation Error', 'Password Mismatch', 'Password and Confirm Password do not match.');
}

$fullName = "{$firstname} {$middlename} {$lastname}";
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    require_once __DIR__ . '/db_connect.php';

    $checkStatement = $mysqli->prepare(
        'SELECT 1 FROM students WHERE student_uid = ? OR email = ? LIMIT 1'
    );
    $checkStatement->bind_param('ss', $collegeId, $email);
    $checkStatement->execute();
    $checkStatement->store_result();

    if ($checkStatement->num_rows > 0) {
        renderPage('Registration Error', 'Duplicate Entry', 'A student with this Enrollment ID or Email is already registered.');
    }

    $insertStatement = $mysqli->prepare(
        'INSERT INTO students
            (student_uid, name, email, password_hash, dob, gender, phone, address, course, study_year)
         VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $insertStatement->bind_param(
        'ssssssssss',
        $collegeId,
        $fullName,
        $email,
        $passwordHash,
        $dob,
        $gender,
        $phone,
        $address,
        $course,
        $year
    );
    $insertStatement->execute();
} catch (mysqli_sql_exception $exception) {
    if ($exception->getCode() === 1062) {
        renderPage('Registration Error', 'Duplicate Entry', 'A student with this Enrollment ID or Email is already registered.');
    }

    error_log('Student registration database error: ' . $exception->getMessage());
    renderPage('Database Error', 'Registration Could Not Be Completed', 'A database error occurred. Please try again later.');
}

$safeFullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$safeCollegeId = htmlspecialchars($collegeId, ENT_QUOTES, 'UTF-8');
renderPage(
    'Registration Successful',
    'Registration Completed!',
    "Congratulations <strong>{$safeFullName}</strong>! Your student registration for Enrollment ID <strong>{$safeCollegeId}</strong> has been saved successfully.",
    'success'
);
