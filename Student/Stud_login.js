var form = document.querySelector("form");

form.onsubmit = function (event) {
    event.preventDefault();

    var student = {
        name: form.elements.fullname.value.trim(),
        studentId: form.elements.student_id.value.trim(),
        email: form.elements.email.value.trim(),
        department: form.elements.department.value
    };

    localStorage.setItem("studentUser", JSON.stringify(student));
    window.location.href = "student_dashboard.html";
};