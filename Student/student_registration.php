<?php
header('Content-Type: text/html; charset=utf-8');

function renderPage($title, $heading, $message, $type = "error") {
    $btnText = $type === "success" ? "Proceed to Student Login " : " Go Back to Form";
    $btnLink = $type === "success" ? "Stud_login.html" : "regis_stud.html";
    
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><title>{$title} - Student Registration</title>
    <link rel="stylesheet" href="studentStyle.css">
</head>
<body class="response-page">
    <div class="response-card">
        <div class="status-tag {$type}">{$type}</div>
        <h2 class="card-title">{$heading}</h2>
        <p class="card-msg">{$message}</p>
        <a href="{$btnLink}" class="btn-action">{$btnText}</a>
    </div>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    renderPage("Method Not Allowed", "Invalid Request", "Please submit the student registration form directly.", "error");
}

$firstname  = trim($_POST["f_name"] ?? "");
$middlename = trim($_POST["m_name"] ?? "");
$lastname   = trim($_POST["L_name"] ?? "");
$college_id = trim($_POST["college_id"] ?? "");
$dob        = trim($_POST["dob"] ?? "");
$gender     = trim($_POST["gender"] ?? "male");
$email      = trim($_POST["email"] ?? "");
$phone      = trim($_POST["phone"] ?? "");
$address    = trim($_POST["address"] ?? "");
$course     = trim($_POST["course"] ?? "");
$year       = trim($_POST["year"] ?? "");
$password   = trim($_POST["password"] ?? "");

$nameRegex       = "/^[A-Za-z]{2,50}$/";
$enrollmentRegex = "/^\d{2}(dit|dcs|dce)\d{3}$/";
$emailRegex      = "/^\d{2}(dit|dcs|dce)\d{3}@charusat\.edu\.in$/";
$phoneRegex      = "/^[6-9]\d{9}$/";

if (!preg_match($nameRegex, $firstname)) {
    renderPage("Validation Error", "Invalid First Name", "First Name must contain only letters (2-50 characters).", "error");
}
if (!preg_match($nameRegex, $middlename)) {
    renderPage("Validation Error", "Invalid Middle Name", "Middle Name must contain only letters (2-50 characters).", "error");
}
if (!preg_match($nameRegex, $lastname)) {
    renderPage("Validation Error", "Invalid Last Name", "Last Name must contain only letters (2-50 characters).", "error");
}

if (!preg_match($enrollmentRegex, $college_id)) {
    renderPage("Validation Error", "Invalid Enrollment ID", "Enrollment ID must match format: 2 digits year, dit/dcs/dce, 3 digits (e.g. 25dcs080).", "error");
}

if (!preg_match($emailRegex, $email)) {
    renderPage("Validation Error", "Invalid University Email", "Email must end with @charusat.edu.in and match student format (e.g. 25dcs080@charusat.edu.in).", "error");
}

$emailParts = explode('@', $email);
if (count($emailParts) < 2 || $emailParts[0] !== $college_id) {
    renderPage("Validation Error", "Email Mismatch", "University Email prefix '{$emailParts[0]}' does not match Enrollment ID '{$college_id}'.", "error");
}

if (!preg_match($phoneRegex, $phone)) {
    renderPage("Validation Error", "Invalid Phone Number", "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.", "error");
}

if (empty($course) || empty($year)) {
    renderPage("Validation Error", "Course / Year Required", "Please select your Course Program and Year of Study.", "error");
}

if (empty($password) || strlen($password) < 6) {
    renderPage("Validation Error", "Invalid Password", "Password must be at least 6 characters long.", "error");
}

$dataDir = __DIR__ . '/../data';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0777, true);
}
$csvFile = $dataDir . '/students.csv';

$headers = ["ID", "Firstname", "Middlename", "Lastname", "EnrollmentID", "DOB", "Gender", "Email", "Phone", "Address", "Course", "Year", "Password", "CreatedAt"];

if (!file_exists($csvFile)) {
    $fp = fopen($csvFile, 'w');
    fputcsv($fp, $headers);
    fclose($fp);
}

$nextId = 1;
$fp = fopen($csvFile, 'r');
fgetcsv($fp);

while (($row = fgetcsv($fp)) !== false) {
    if (isset($row[0]) && is_numeric($row[0])) {
        $nextId = max($nextId, (int) $row[0] + 1);
    }
}

fclose($fp);

$createdAt = date('Y-m-d H:i:s');
$newRecord = [
    $nextId,
    $firstname,
    $middlename,
    $lastname,
    $college_id,
    $dob,
    $gender,
    $email,
    $phone,
    $address,
    $course,
    $year,
    $password,
    $createdAt
];

$fp = fopen($csvFile, 'a');
fputcsv($fp, $newRecord);
fclose($fp);

$fullName = "{$firstname} {$middlename} {$lastname}";
renderPage(
    "Registration Successful",
    "Registration Completed!",
    "Congratulations <strong>{$fullName}</strong>! Your student registration for Enrollment ID <strong>{$college_id}</strong> has been saved successfully.",
    "success"
);