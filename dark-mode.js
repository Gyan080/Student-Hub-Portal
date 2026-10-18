
var savedTheme = localStorage.getItem("theme");
if (savedTheme === "dark") {
  document.body.classList.add("dark-mode");
}

document.addEventListener("DOMContentLoaded", function () {
  var toggleButton = document.createElement("button");
  toggleButton.className = "dark-mode-toggle-btn";
  toggleButton.type = "button";

  var isDark = document.body.classList.contains("dark-mode");
  toggleButton.textContent = isDark ? "Light mode" : "Dark mode";
  toggleButton.setAttribute("aria-pressed", isDark ? "true" : "false");

  toggleButton.addEventListener("click", function () {
    var darkModeEnabled = document.body.classList.toggle("dark-mode");
    if (darkModeEnabled) {
      localStorage.setItem("theme", "dark");
      toggleButton.textContent = "Light mode";
      toggleButton.setAttribute("aria-pressed", "true");
    } else {
      localStorage.setItem("theme", "light");
      toggleButton.textContent = "Dark mode";
      toggleButton.setAttribute("aria-pressed", "false");
    }
  });

  document.body.appendChild(toggleButton);
});
