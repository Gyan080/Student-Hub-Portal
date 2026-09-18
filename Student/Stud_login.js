document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("studentLoginForm");
  if (!loginForm) return;

  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const studentId = document.getElementById("student_id").value.trim();
    const fullname = document.getElementById("fullname").value.trim();
    const personalEmail = document.getElementById("personalEmail").value.trim();
    const collegeEmail = document.getElementById("collegeEmail").value.trim();
    const department = document.getElementById("department").value;
    const password = document.getElementById("password").value.trim();

    if (!studentId || !fullname || !personalEmail || !collegeEmail || !department || !password) {
      alert("Please fill in all required fields.");
      return;
    }

    const idRegex = /^\d{2}(DCS|DIT|DCE|DAIML)\d{3}$/i;
    if (!idRegex.test(studentId)) {
      alert("Please enter a valid Enrollment ID format (Example: 25DCS001, 25DIT010, 25DCE123, 25DAIML178).");
      document.getElementById("student_id").focus();
      return;
    }

    const personalEmailRegex = /^[A-Za-z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i;
    const universityEmailRegex = /^\d{2}(DCS|DIT|DCE|DAIML)\d{3}@charusat\.edu\.in$/i;
    if (!personalEmailRegex.test(personalEmail)) {
      alert("Personal email must end with @gmail.com or @yahoo.com.");
      document.getElementById("personalEmail").focus();
      return;
    }
    if (!universityEmailRegex.test(collegeEmail) || !collegeEmail.toLowerCase().startsWith(studentId.toLowerCase())) {
      alert("College email must be in the format 25dcs001@charusat.edu.in and match your student ID.");
      document.getElementById("collegeEmail").focus();
      return;
    }

    const serialNumber = Number(studentId.slice(5));
    if (serialNumber < 1 || serialNumber > 178) {
      alert("Enrollment number must be between 001 and 178.");
      document.getElementById("student_id").focus();
      return;
    }

    const allStudents = await loadStudentProfiles();
    const matchedUser = allStudents.find((student) => {
      const sameId = student.studentId && student.studentId.toUpperCase() === studentId.toUpperCase();
      const samePersonalEmail = student.personalEmail && student.personalEmail.toLowerCase() === personalEmail.toLowerCase();
      const sameCollegeEmail = student.collegeEmail && student.collegeEmail.toLowerCase() === collegeEmail.toLowerCase();
      const sameName = student.name && student.name.toLowerCase() === fullname.toLowerCase();
      const sameDepartment = student.department && student.department === department;
      const samePassword = String(student.password || "") === password;
      return sameId && samePersonalEmail && sameCollegeEmail && sameName && sameDepartment && samePassword;
    });

    if (!matchedUser) {
      showBrutalistModal(
        "Login Failed",
        "Student record not found. Please check your enrollment ID, name, email, department, and password.",
        "error",
        function () {
          document.getElementById("student_id").focus();
        }
      );
      return;
    }

    const studentUser = {
      ...matchedUser,
      name: matchedUser.name,
      studentId: matchedUser.studentId,
      personalEmail: matchedUser.personalEmail,
      collegeEmail: matchedUser.collegeEmail,
      department: matchedUser.department,
      role: "Student",
      loginTime: new Date().toLocaleTimeString()
    };

    localStorage.setItem("studentUser", JSON.stringify(studentUser));
    window.location.href = "DashBoard/student_dashboard.html";
  });
});

async function loadStudentProfiles() {
  const savedProfiles = JSON.parse(localStorage.getItem("studentProfiles") || "[]");

  try {
    const response = await fetch("../data/studentProfiles.json");
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