var form = document.querySelector("form");

form.onsubmit = function (event) {
    event.preventDefault();
    window.location.href = "student_dashboard.html";
};