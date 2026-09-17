// PDF Unit 4 & 5: DOM Manipulation and Event Handling
document.addEventListener("DOMContentLoaded", function () {
  var dashboardLayout = document.querySelector(".dashboard-layout");
  var sidebar = dashboardLayout ? dashboardLayout.querySelector("aside, .dashboard-sidebar") : null;
  var student = JSON.parse(localStorage.getItem("studentUser") || "null");

  function setStudentText(id, value) {
    var element = document.getElementById(id);
    if (element && value) {
      element.textContent = value;
    }
  }

  if (student) {
    setStudentText("student-name", student.name);
    setStudentText("sidebar-user-name", student.name);
    setStudentText("student-id", student.studentId);
    setStudentText("sidebar-user-id", student.studentId);
    setStudentText("student-email", student.email);
    setStudentText("student-department", student.department);

    var initialsEl = document.getElementById("avatar-initials");
    if (initialsEl && student.name) {
      var parts = student.name.trim().split(" ");
      var init = parts[0] ? parts[0].charAt(0) : "S";
      if (parts.length > 1) init += parts[parts.length - 1].charAt(0);
      initialsEl.textContent = init.toUpperCase();
    }
  }

  if (dashboardLayout && sidebar) {
    var existingBtn = dashboardLayout.querySelector(".menu-button");
    if (!existingBtn) {
      var menuButton = document.createElement("button");
      menuButton.className = "menu-button";
      menuButton.type = "button";
      menuButton.textContent = "☰ Portal Navigation Menu";
      menuButton.setAttribute("aria-expanded", "false");

      dashboardLayout.insertBefore(menuButton, sidebar);

      menuButton.onclick = function () {
        var isOpen = dashboardLayout.classList.toggle("menu-open");
        menuButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
      };

      var menuLinks = sidebar.querySelectorAll("a");
      menuLinks.forEach(function (link) {
        link.onclick = function () {
          dashboardLayout.classList.remove("menu-open");
          menuButton.setAttribute("aria-expanded", "false");
        };
      });
    }
  }
});

