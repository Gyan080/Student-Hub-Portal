document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("studentLoginForm");
  if (!loginForm) return;

  loginForm.addEventListener("submit", function (event) {
    const studentId = document.getElementById("student_id").value.trim();
    const password = document.getElementById("password").value;

    if (!studentId || !password) {
      event.preventDefault();
      alert("Please enter both your Enrollment ID/Email and password.");
      return;
    }

  });
});