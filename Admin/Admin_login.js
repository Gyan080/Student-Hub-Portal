document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("adminLoginForm");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    const fullname = document.getElementById("fullname") ? document.getElementById("fullname").value.trim() : "";
    const email = document.getElementById("email") ? document.getElementById("email").value.trim() : "";
    const adminId = document.getElementById("admin_id") ? document.getElementById("admin_id").value.trim() : "";
    const password = document.getElementById("password") ? document.getElementById("password").value : "";

    if (!fullname || !email || !adminId || !password) {
      event.preventDefault();
      alert("Please fill in Full Name, Email, Staff ID, and Password.");
      return;
    }

    // Allow standard POST submission to Admin_login.php for session processing
  });
});
