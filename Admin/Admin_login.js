var form = document.querySelector("form");

form.onsubmit = function (event) {
    event.preventDefault();
    var admin = {
        name: form.elements.fullname.value.trim(),
        role: form.elements.campus_role.value,
        email: form.elements.email.value.trim(),
        department: form.elements.dept.value.trim()
    };

    localStorage.setItem("adminUser", JSON.stringify(admin));
    window.location.href = "admin_dashboard.html";
};