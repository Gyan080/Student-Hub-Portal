var banner = document.getElementById("noticeBanner");
if (banner) {
  if (localStorage.getItem("bannerClosed") === "yes") {
    banner.style.display = "none";
  }
}

var closeBtn = document.getElementById("bannerCloseBtn");
if (closeBtn) {
  closeBtn.addEventListener("click", function () {
    banner.style.display = "none";
    localStorage.setItem("bannerClosed", "yes");
  });
}
