// ===== Admin Sign In Handler (PDF Unit 3.1 & 4.5 & 5.1) =====
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("adminLoginForm");
  if (!form) return;

  form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const fullname = document.getElementById("fullname").value.trim();
    const role = document.getElementById("campus_role").value;
    const personalEmail = document.getElementById("personalEmail").value.trim();
    const collegeEmail = document.getElementById("collegeEmail").value.trim();
    const dept = document.getElementById("dept").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!fullname || !role || !personalEmail || !collegeEmail || !dept || !password) {
      alert("Please fill in all required fields.");
      return;
    }

    const personalEmailRegex = /^[A-Za-z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i;
    const universityEmailRegex = /^[A-Za-z]+(?:\.[A-Za-z]+)?@charusat\.ac\.in$/i;
    if (!personalEmailRegex.test(personalEmail)) {
      alert("Personal email must end with @gmail.com or @yahoo.com.");
      document.getElementById("personalEmail").focus();
      return;
    }
    if (!universityEmailRegex.test(collegeEmail)) {
      alert("Official college email must be in the format firstname@charusat.ac.in or firstname.lastname@charusat.ac.in.");
      document.getElementById("collegeEmail").focus();
      return;
    }

    const allAdmins = await loadAdminProfiles();
    const matchedAdmin = allAdmins.find((admin) => {
      const sameName = admin.name && admin.name.toLowerCase() === fullname.toLowerCase();
      const sameRole = admin.role && admin.role === role;
      const samePersonalEmail = admin.personalEmail && admin.personalEmail.toLowerCase() === personalEmail.toLowerCase();
      const sameCollegeEmail = admin.collegeEmail && admin.collegeEmail.toLowerCase() === collegeEmail.toLowerCase();
      const sameDept = admin.department && admin.department === dept;
      const samePassword = String(admin.password || "") === password;
      return sameName && sameRole && samePersonalEmail && sameCollegeEmail && sameDept && samePassword;
    });

    if (!matchedAdmin) {
      showBrutalistModal(
        "Login Failed",
        "Admin account not found. Please verify your name, role, email, department, and password.",
        "error",
        function () {
          document.getElementById("fullname").focus();
        }
      );
      return;
    }

    const adminUser = {
      ...matchedAdmin,
      name: matchedAdmin.name,
      role: matchedAdmin.role,
      personalEmail: matchedAdmin.personalEmail,
      collegeEmail: matchedAdmin.collegeEmail,
      department: matchedAdmin.department,
      loginTime: new Date().toLocaleTimeString()
    };

    localStorage.setItem("adminUser", JSON.stringify(adminUser));
    window.location.href = "DashBoard/admin_dashboard.html";
  });
});

async function loadAdminProfiles() {
  const savedProfiles = JSON.parse(localStorage.getItem("adminProfiles") || "[]");

  try {
    const response = await fetch("../data/adminProfiles.json");
    if (!response.ok) return savedProfiles;
    const jsonProfiles = await response.json();
    return [...jsonProfiles, ...savedProfiles];
  } catch (error) {
    return savedProfiles;
  }
}

function showBrutalistModal(title, message, type, callback) {
  const existingModal = document.getElementById("customBrutalistModal");
  if (existingModal) existingModal.remove();

  const backdrop = document.createElement("div");
  backdrop.id = "customBrutalistModal";
  backdrop.style.position = "fixed";
  backdrop.style.inset = "0";
  backdrop.style.backgroundColor = "rgba(17, 24, 39, 0.6)";
  backdrop.style.display = "flex";
  backdrop.style.alignItems = "center";
  backdrop.style.justifyContent = "center";
  backdrop.style.zIndex = "9999";
  backdrop.style.padding = "16px";

  const modalBox = document.createElement("div");
  modalBox.style.backgroundColor = "#FFFFFF";
  modalBox.style.border = "2.5px solid #1A1A1A";
  modalBox.style.borderRadius = "8px";
  modalBox.style.boxShadow = "6px 6px 0px #1A1A1A";
  modalBox.style.maxWidth = "460px";
  modalBox.style.width = "100%";
  modalBox.style.padding = "24px";

  const tag = document.createElement("div");
  tag.textContent = type === "success" ? "SUCCESS" : "NOTICE";
  tag.style.display = "inline-block";
  tag.style.fontSize = "0.75rem";
  tag.style.fontWeight = "800";
  tag.style.padding = "3px 8px";
  tag.style.borderRadius = "4px";
  tag.style.border = "1.5px solid #1A1A1A";
  tag.style.marginBottom = "10px";
  tag.style.backgroundColor = type === "success" ? "#D1FAE5" : "#FEE2E2";
  tag.style.color = type === "success" ? "#065F46" : "#B91C1C";

  const h3 = document.createElement("h3");
  h3.textContent = title;
  h3.style.fontSize = "1.3rem";
  h3.style.fontWeight = "800";
  h3.style.color = "#111827";
  h3.style.marginBottom = "10px";

  const p = document.createElement("p");
  p.textContent = message;
  p.style.fontSize = "0.95rem";
  p.style.color = "#4B5563";
  p.style.marginBottom = "20px";
  p.style.lineHeight = "1.6";

  const btn = document.createElement("button");
  btn.textContent = "Continue";
  btn.style.width = "100%";
  btn.style.padding = "11px";
  btn.style.backgroundColor = "#1F4E79";
  btn.style.color = "#FFFFFF";
  btn.style.fontSize = "0.95rem";
  btn.style.fontWeight = "700";
  btn.style.border = "2px solid #1A1A1A";
  btn.style.borderRadius = "6px";
  btn.style.boxShadow = "3px 3px 0px #1A1A1A";
  btn.style.cursor = "pointer";

  btn.addEventListener("click", function () {
    backdrop.remove();
    if (callback) callback();
  });

  modalBox.appendChild(tag);
  modalBox.appendChild(h3);
  modalBox.appendChild(p);
  modalBox.appendChild(btn);
  backdrop.appendChild(modalBox);
  document.body.appendChild(backdrop);
}
