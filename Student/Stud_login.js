document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("studentLoginForm");
  if (!loginForm) return;

  loginForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const studentId = document.getElementById("student_id").value.trim();
    const fullname = document.getElementById("fullname").value.trim();
    const email = document.getElementById("email").value.trim();
    const department = document.getElementById("department").value;

    if (!studentId || !fullname || !email || !department) {
      alert("Please fill in all required fields.");
      return;
    }

    const idRegex = /^\d{2}[A-Za-z]{3}\d{3}$/i;
    if (!idRegex.test(studentId)) {
      alert("Please enter a valid Enrollment ID format (e.g. 25dcs080).");
      document.getElementById("student_id").focus();
      return;
    }

    const studentUser = {
      name: fullname,
      studentId: studentId.toUpperCase(),
      email: email,
      department: department,
      role: "Student",
      loginTime: new Date().toLocaleTimeString()
    };

    localStorage.setItem("studentUser", JSON.stringify(studentUser));
    window.location.href = "DashBoard/student_dashboard.html";
  });
});