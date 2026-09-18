document.addEventListener("DOMContentLoaded", function () {
  const regForm = document.getElementById("studentRegForm");
  if (!regForm) return;

  regForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    const firstname = document.getElementById("firstname").value.trim();
    const middlename = document.getElementById("middlename").value.trim();
    const lastname = document.getElementById("lastname").value.trim();
    const college_id = document.getElementById("college_id").value.trim();
    const dobVal = document.getElementById("dob").value;
    const personalEmail = document.getElementById("personalEmail").value.trim();
    const collegeEmail = document.getElementById("collegeEmail").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value.trim();
    const address = document.getElementById("address").value.trim();
    const guardianName = document.getElementById("guardianName").value.trim();
    const guardianContact = document.getElementById("guardianContact").value.trim();
    const bloodGroup = document.getElementById("bloodGroup").value;
    const healthDetails = document.getElementById("healthDetails").value.trim();
    const course = document.getElementById("course").value;
    const year = document.getElementById("year").value;
    const currentSemester = document.getElementById("currentSemester").value;
    const academicCounselor = document.getElementById("academicCounselor").value.trim();
    const cgpa = document.getElementById("cgpa").value;

    const genderRadios = document.getElementsByName("gender");
    let gender = "male";
    for (let i = 0; i < genderRadios.length; i++) {
      if (genderRadios[i].checked) {
        gender = genderRadios[i].value;
        break;
      }
    }

    const nameRegex = /^[A-Za-z]{2,50}$/;
    const departmentCodeMap = {
      "Information Technology": "DIT",
      "Computer Science": "DCS",
      "Computer Engineering": "DCE",
      "AI & Machine Learning": "DAIML"
    };
    const enrollmentRegex = /^\d{2}(DCS|DIT|DCE|DAIML)\d{3}$/i;
    const personalEmailRegex = /^[A-Za-z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i;
    const universityEmailRegex = /^\d{2}(DCS|DIT|DCE|DAIML)\d{3}@charusat\.edu\.in$/i;
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

    if (!enrollmentRegex.test(college_id)) {
      showBrutalistModal("Validation Error", "Enrollment ID must match format: YY + Department Code + 3-digit number. Example: 25DCS001, 25DIT010, 25DCE123, 25DAIML178.", "error");
      document.getElementById("college_id").focus();
      return;
    }

    const enteredCode = college_id.slice(2, 5).toUpperCase();
    const expectedCode = departmentCodeMap[course];
    if (enteredCode !== expectedCode) {
      showBrutalistModal("Validation Error", `The department code in your ID must match the selected department: ${expectedCode}.`, "error");
      document.getElementById("college_id").focus();
      return;
    }

    const serialNumber = Number(college_id.slice(5));
    if (serialNumber < 1 || serialNumber > 178) {
      showBrutalistModal("Validation Error", "The last 3 digits must be between 001 and 178.", "error");
      document.getElementById("college_id").focus();
      return;
    }

    if (!dobVal) {
      showBrutalistModal("Validation Error", "Please select your Date of Birth.", "error");
      document.getElementById("dob").focus();
      return;
    }

    const isPersonalEmail = personalEmailRegex.test(personalEmail);
    const studentId = college_id.toUpperCase();
    const isUniversityEmail = universityEmailRegex.test(collegeEmail) && collegeEmail.toLowerCase().startsWith(studentId.toLowerCase());
    if (!isPersonalEmail) {
      showBrutalistModal("Validation Error", "Personal email must end with @gmail.com or @yahoo.com.", "error");
      document.getElementById("personalEmail").focus();
      return;
    }
    if (!isUniversityEmail) {
      showBrutalistModal("Validation Error", "College email must match the format 25dcs001@charusat.edu.in and use your student ID.", "error");
      document.getElementById("collegeEmail").focus();
      return;
    }

    if (!phoneRegex.test(phone)) {
      showBrutalistModal("Validation Error", "Phone number must be exactly 10 digits starting with 6, 7, 8, or 9.", "error");
      document.getElementById("phone").focus();
      return;
    }

    if (!phoneRegex.test(guardianContact)) {
      showBrutalistModal("Validation Error", "Guardian contact must be exactly 10 digits starting with 6, 7, 8, or 9.", "error");
      document.getElementById("guardianContact").focus();
      return;
    }

    if (!password || password.length < 6) {
      showBrutalistModal("Validation Error", "Password must be at least 6 characters long.", "error");
      document.getElementById("password").focus();
      return;
    }

    if (!course || !year || !bloodGroup || !currentSemester || !guardianName || !healthDetails || !academicCounselor || !cgpa) {
      showBrutalistModal("Validation Error", "Please complete all academic, health, guardian, and profile fields.", "error");
      return;
    }

    if (Number(cgpa) < 0 || Number(cgpa) > 10) {
      showBrutalistModal("Validation Error", "Cumulative CGPA must be between 0 and 10.", "error");
      document.getElementById("cgpa").focus();
      return;
    }

    const fullName = `${firstname} ${middlename} ${lastname}`.trim();
    const profileData = await loadStudentProfiles();
    const enteredPersonalEmail = personalEmail.toLowerCase();
    const enteredCollegeEmail = collegeEmail.toLowerCase();
    const duplicate = profileData.find((student) => {
      return (student.studentId && student.studentId.toUpperCase() === studentId) ||
             (student.personalEmail && student.personalEmail.toLowerCase() === enteredPersonalEmail) ||
             (student.collegeEmail && student.collegeEmail.toLowerCase() === enteredCollegeEmail) ||
             (student.phone && student.phone === phone);
    });

    if (duplicate) {
      showBrutalistModal(
        "Duplicate Student Profile",
        "This enrollment ID, email, or phone number already exists. Please login or use a different registration detail.",
        "error",
        function () {
          window.location.href = "Stud_login.html";
        }
      );
      return;
    }

    const studentUser = {
      id: Date.now(),
      name: fullName,
      studentId: studentId,
      personalEmail: enteredPersonalEmail,
      collegeEmail: enteredCollegeEmail,
      phone: phone,
      password: password,
      gender: gender,
      dob: dobVal,
      address: address,
      guardianName: guardianName,
      guardianContact: guardianContact,
      bloodGroup: bloodGroup,
      healthDetails: healthDetails,
      department: course,
      year: year,
      currentSemester: currentSemester,
      academicCounselor: academicCounselor,
      cgpa: Number(cgpa),
      role: "Student",
      status: "Active",
      registeredAt: new Date().toLocaleDateString()
    };

    const allStudents = [...profileData, studentUser];
    localStorage.setItem("studentProfiles", JSON.stringify(allStudents));

    showBrutalistModal(
      "Registration Successful!",
      `Welcome ${fullName}! Your student account has been saved and redirected to login.`,
      "success",
      function () {
        window.location.href = "Stud_login.html";
      }
    );
    setTimeout(function () {
      window.location.href = "Stud_login.html";
    }, 1500);
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

async function saveProfileToJson(profileType, profile) {
  try {
    await fetch("/api/" + profileType, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(profile)
    });
  } catch (error) {
    // Local storage remains available when the page is opened without server.js.
  }
}