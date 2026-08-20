var dashboardLayout = document.querySelector(".dashboard-layout");
var sidebar = dashboardLayout ? dashboardLayout.querySelector("aside") : null;

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
