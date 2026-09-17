var dashboardLayout = document.querySelector(".dashboard-layout");
var sidebar = dashboardLayout ? dashboardLayout.querySelector("aside") : null;
var student = JSON.parse(localStorage.getItem("studentUser") || "null");

function setStudentText(id, value) {
    var element = document.getElementById(id);

    if (element) {
        element.textContent = value || "Not provided";
    }
}

if (student) {
    setStudentText("student-name", student.name);
    setStudentText("student-id", student.studentId);
    setStudentText("student-email", student.email);
    setStudentText("student-department", student.department);

    if (sidebar) {
        var userInfo = document.createElement("p");
        userInfo.className = "logged-in-user";
        userInfo.textContent = "Logged in: " + (student.name || "Student");
        sidebar.querySelector("nav").appendChild(userInfo);
    }
}

if (dashboardLayout && sidebar) {
    var menuButton = document.createElement("button");
    menuButton.className = "menu-button";
    menuButton.type = "button";
    menuButton.textContent = "☰ Menu";
    menuButton.setAttribute("aria-expanded", "false");

    dashboardLayout.insertBefore(menuButton, sidebar);

    menuButton.onclick = function () {
        var isOpen = dashboardLayout.classList.toggle("menu-open");
        menuButton.setAttribute("aria-expanded", isOpen);
    };

    var menuLinks = sidebar.querySelectorAll("a");
    menuLinks.forEach(function (link) {
        link.onclick = function () {
            dashboardLayout.classList.remove("menu-open");
            menuButton.setAttribute("aria-expanded", "false");
        };
    });
}
