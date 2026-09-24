document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("adminLoginForm");
  if (!form) return;

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const fullname = document.getElementById("fullname").value.trim();
    const role = document.getElementById("campus_role").value;
    const email = document.getElementById("email").value.trim();
    const dept = document.getElementById("dept").value.trim();

    if (!fullname || !role || !email || !dept) {
      alert("Please fill in all required fields.");
      return;
    }

    const emailRegex = /^[\w.-]+@[\w-]+\.[a-z]{2,}$/i;
    if (!emailRegex.test(email)) {
      alert("Please enter a valid official university email address.");
      document.getElementById("email").focus();
      return;
    }

    const admin = {
      name: fullname,
      role: role,
      email: email,
      department: dept,
      loginTime: new Date().toLocaleTimeString()
    };

    localStorage.setItem("adminUser", JSON.stringify(admin));
    window.location.href = "DashBoard/admin_dashboard.html";
  });
});
