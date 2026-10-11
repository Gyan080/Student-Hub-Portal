document.addEventListener("DOMContentLoaded", function () {
  var toggleButton = document.createElement("button");
  toggleButton.className = "dark-mode-toggle-btn";
  toggleButton.type = "button";
  toggleButton.textContent = "Dark mode";
  toggleButton.setAttribute("aria-pressed", "false");

  toggleButton.addEventListener("click", function () {
    var darkModeEnabled = document.body.classList.toggle("dark-mode");
    toggleButton.textContent = darkModeEnabled ? "Light mode" : "Dark mode";
    toggleButton.setAttribute("aria-pressed", darkModeEnabled);
  });

  document.body.appendChild(toggleButton);
});
