var form = document.querySelector("form");

form.onsubmit = function (event) {
    event.preventDefault();

    var firstname = document.getElementById("firstname").value.trim();
    var middlename = document.getElementById("middlename").value.trim();
    var lastname = document.getElementById("lastname").value.trim();
    var college_id = document.getElementById("college_id").value.trim();
    var dobVal = document.getElementById("dob").value;
    var email = document.getElementById("email").value.trim();
    var phone = document.getElementById("phone").value.trim();
    var address = document.getElementById("address").value.trim();
    var course = document.getElementById("course").value;
    var year = document.getElementById("year").value;

    var genderRadios = document.getElementsByName("gender");
    var genderSelected = false;
    for (var i = 0; i < genderRadios.length; i++) {
        if (genderRadios[i].checked) {
            genderSelected = true;
            break;
        }
    }

    var nameRegex = /^[A-Za-z]{2,50}$/;
    var enrollmentRegex = /^\d{2}[A-Za-z]{3}\d{3}$/;
    var emailRegex = /^[a-zA-Z0-9._%+-]+@charusat\.(edu|ac)\.in$/;
    var phoneRegex = /^[6-9]\d{9}$/;

    if (!nameRegex.test(firstname)) {
        alert("First Name must contain only letters and be 2-50 characters long.");
        document.getElementById("firstname").focus();
        return false;
    }

    if (!nameRegex.test(middlename)) {
        alert("Middle Name must contain only letters and be 2-50 characters long.");
        document.getElementById("middlename").focus();
        return false;
    }

    if (!nameRegex.test(lastname)) {
        alert("Last Name must contain only letters and be 2-50 characters long.");
        document.getElementById("lastname").focus();
        return false;
    }

    if (!enrollmentRegex.test(college_id)) {
        alert("Enrollment ID must be in the format: 2 digits, 3 letters, 3 digits (e.g. 25dcs080).");
        document.getElementById("college_id").focus();
        return false;
    }

    if (!dobVal) {
        alert("Please select your Date of Birth.");
        document.getElementById("dob").focus();
        return false;
    }

    var dobDate = new Date(dobVal);
    var today = new Date();
    var age = today.getFullYear() - dobDate.getFullYear();
    var monthDiff = today.getMonth() - dobDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
        age--;
    }
    if (age < 15 || age > 100) {
        alert("Student must be at least 15 years old and age must be valid.");
        document.getElementById("dob").focus();
        return false;
    }

    if (!genderSelected) {
        alert("Please select your Gender.");
        return false;
    }

    if (!emailRegex.test(email)) {
        alert("Please enter a valid Charusat email address (ending with @charusat.edu.in or @charusat.ac.in).");
        document.getElementById("email").focus();
        return false;
    }

    if (!phoneRegex.test(phone)) {
        alert("Phone number must be exactly 10 digits and start with 6, 7, 8, or 9.");
        document.getElementById("phone").focus();
        return false;
    }

    if (address.length < 10 || address.length > 200) {
        alert("Address must be between 10 and 200 characters long.");
        document.getElementById("address").focus();
        return false;
    }

    if (!course) {
        alert("Please select a Course.");
        document.getElementById("course").focus();
        return false;
    }

    if (!year) {
        alert("Please select your Year of Study.");
        document.getElementById("year").focus();
        return false;
    }

    showPopup("Student registration successful!");
    return true;
};

function showPopup(message) {
    var popup = document.createElement("div");
    popup.innerHTML = "<div style='background:white; padding:25px; text-align:center; border-radius:8px;'><p>" + message + "</p><button onclick='this.parentElement.parentElement.remove()'>OK</button></div>";
    popup.style = "position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index: 1000;";
    document.body.appendChild(popup);
}
