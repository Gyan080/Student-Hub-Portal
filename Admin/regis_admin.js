// ===== Admin Registration Handler (PDF Unit 3.1 & 4.3 & 4.4) =====
document.addEventListener("DOMContentLoaded", function () {
  const regForm = document.getElementById("adminRegForm");
  if (!regForm) return;

  regForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const firstname = document.getElementById("firstname").value.trim();
    const middlename = document.getElementById("middlename").value.trim();
    const lastname = document.getElementById("lastname").value.trim();
    const admin_id = document.getElementById("admin_id").value.trim();
    const dobVal = document.getElementById("dob").value;
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address").value.trim();
    const department = document.getElementById("department").value;
    const role = document.getElementById("role").value;
    const fileInput = document.getElementById("affiliation_proof");
    const guidelines = document.getElementById("guidelines").checked;

    const genderRadios = document.getElementsByName("gender");
    let gender = "male";
    for (let i = 0; i < genderRadios.length; i++) {
      if (genderRadios[i].checked) {
        gender = genderRadios[i].value;
        break;
      }
    }

    const nameRegex = /^[A-Za-z]{2,50}$/;
    const adminIdRegex = /^ADM\d{5}$/i;
    const emailRegex = /^[\w.-]+@[\w-]+\.[a-z]{2,}$/i;
    const phoneRegex = /^[6-9]\d{9}$/;

    if (!nameRegex.test(firstname)) {
      showBrutalistModal("Validation Error", "First Name must contain only letters (2-50 characters).", "error");
      document.getElementById("firstname").focus();
      return;
    }

    if (!nameRegex.test(middlename)) {
      showBrutalistModal("Validation Error", "Middle Name must contain only letters (2-50 characters).", "error");
      document.getElementById("middlename").focus();
      return;
    }

    if (!nameRegex.test(lastname)) {
      showBrutalistModal("Validation Error", "Last Name must contain only letters (2-50 characters).", "error");
      document.getElementById("lastname").focus();
      return;
    }

    if (!adminIdRegex.test(admin_id)) {
      showBrutalistModal("Validation Error", "Admin ID must start with 'ADM' followed by 5 digits (e.g. ADM10025).", "error");
      document.getElementById("admin_id").focus();
      return;
    }

    if (!dobVal) {
      showBrutalistModal("Validation Error", "Please select your Date of Birth.", "error");
      document.getElementById("dob").focus();
      return;
    }

    if (!emailRegex.test(email)) {
      showBrutalistModal("Validation Error", "Please enter a valid official university email address.", "error");
      document.getElementById("email").focus();
      return;
    }

    if (!phoneRegex.test(phone)) {
      showBrutalistModal("Validation Error", "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.", "error");
      document.getElementById("phone").focus();
      return;
    }

    if (!department || !role) {
      showBrutalistModal("Validation Error", "Please select your Assigned Department and Administrative Role.", "error");
      return;
    }

    if (!fileInput.files || fileInput.files.length === 0) {
      showBrutalistModal("Validation Error", "Please select your Institutional ID proof document.", "error");
      fileInput.focus();
      return;
    }

    if (!guidelines) {
      showBrutalistModal("Validation Error", "You must verify compliance with University Regulations.", "error");
      document.getElementById("guidelines").focus();
      return;
    }

    const fullName = `${firstname} ${middlename} ${lastname}`.trim();
    const adminUser = {
      name: fullName,
      adminId: admin_id.toUpperCase(),
      role: role,
      email: email,
      phone: phone,
      gender: gender,
      department: department,
      address: address,
      loginTime: new Date().toLocaleTimeString()
    };

    localStorage.setItem("adminUser", JSON.stringify(adminUser));

    showBrutalistModal(
      "Admin Profile Registered! 🎓",
      `Welcome ${fullName}! Your official administrative account has been established. Redirecting to Admin Dashboard...`,
      "success",
      function () {
        window.location.href = "DashBoard/admin_dashboard.html";
      }
    );
  });
});

// PDF Unit 4.4 DOM Modal
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
  tag.textContent = type === "success" ? "VERIFIED" : "ATTENTION";
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

