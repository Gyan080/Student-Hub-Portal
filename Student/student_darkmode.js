// Student Portal Dark Mode Toggle Script (Minimal, Clean SVG Icons, No LocalStorage)
document.addEventListener("DOMContentLoaded", function () {
  var moonSvg = '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
  var sunSvg = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5" fill="currentColor"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';

  var toggleBtn = document.createElement("button");
  toggleBtn.className = "dark-mode-toggle-btn";
  toggleBtn.setAttribute("type", "button");
  toggleBtn.setAttribute("aria-label", "Toggle Dark Mode");
  toggleBtn.title = "Toggle Dark Mode";
  toggleBtn.innerHTML = moonSvg;

  toggleBtn.addEventListener("click", function () {
    var isDark = document.body.classList.toggle("dark-mode");
    toggleBtn.innerHTML = isDark ? sunSvg : moonSvg;
  });

  document.body.appendChild(toggleBtn);
});
