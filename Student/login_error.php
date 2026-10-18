<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login Failed - Student Portal</title>
  <link rel="stylesheet" href="studentStyle.css">
</head>
<body class="response-page">
  <div class="response-card">
    <div class="status-tag error">error</div>
    <h2 class="card-title">Login Failed</h2>
    <p class="card-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="Stud_login.html" class="btn-action">Go Back to Login</a>
  </div>
</body>
</html>
