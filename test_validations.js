// Verification script for Student & Admin validations
const assert = require('assert');

// 1. Regular Expressions
const nameRegex = /^[A-Za-z]{2,50}$/;
const enrollmentRegex = /^\d{2}[A-Za-z]{3}\d{3}$/;
const adminIdRegex = /^ADM\d{5}$/i;
const emailRegex = /^[a-zA-Z0-9._%+-]+@charusat\.(edu|ac)\.in$/;
const phoneRegex = /^[6-9]\d{9}$/;

// Age calculation logic
function calculateAge(dobVal) {
    const dobDate = new Date(dobVal);
    const today = new Date();
    let age = today.getFullYear() - dobDate.getFullYear();
    const monthDiff = today.getMonth() - dobDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dobDate.getDate())) {
        age--;
    }
    return age;
}

// Run Tests
try {
    console.log("Running Name validations...");
    assert.strictEqual(nameRegex.test("John"), true);
    assert.strictEqual(nameRegex.test("A"), false); // too short
    assert.strictEqual(nameRegex.test("John123"), false); // no numbers allowed
    assert.strictEqual(nameRegex.test("John-Doe"), false); // no hyphens in this strict pattern
    assert.strictEqual(nameRegex.test("VeryLongNameThatExceedsFiftyCharactersJustToTestTheRegexConstraint"), false);

    console.log("Running Enrollment ID validations...");
    assert.strictEqual(enrollmentRegex.test("25dcs080"), true);
    assert.strictEqual(enrollmentRegex.test("23it001"), false); // only 2 digits for roll, should be 3
    assert.strictEqual(enrollmentRegex.test("23it0012"), false); // too many digits
    assert.strictEqual(enrollmentRegex.test("abcdcs123"), false); // non-digits at start

    console.log("Running Admin ID validations...");
    assert.strictEqual(adminIdRegex.test("ADM12345"), true);
    assert.strictEqual(adminIdRegex.test("adm12345"), true); // case insensitive
    assert.strictEqual(adminIdRegex.test("ADM1234"), false); // too short
    assert.strictEqual(adminIdRegex.test("ADM123456"), false); // too long
    assert.strictEqual(adminIdRegex.test("BAD12345"), false); // wrong prefix

    console.log("Running Email validations...");
    assert.strictEqual(emailRegex.test("john@charusat.edu.in"), true);
    assert.strictEqual(emailRegex.test("doe@charusat.ac.in"), true);
    assert.strictEqual(emailRegex.test("john@charusat.org"), false); // invalid domain
    assert.strictEqual(emailRegex.test("john.doe@gmail.com"), false); // invalid domain

    console.log("Running Phone validations...");
    assert.strictEqual(phoneRegex.test("9876543210"), true);
    assert.strictEqual(phoneRegex.test("5876543210"), false); // invalid starting digit
    assert.strictEqual(phoneRegex.test("987654321"), false); // too short
    assert.strictEqual(phoneRegex.test("98765432100"), false); // too long

    console.log("Running Age calculations (Student >= 15)...");
    assert.strictEqual(calculateAge("2011-01-01") >= 15, true); // Age 15 in 2026
    assert.strictEqual(calculateAge("2012-01-01") >= 15, false); // Age 14 in 2026

    console.log("Running Age calculations (Admin >= 18)...");
    assert.strictEqual(calculateAge("2008-01-01") >= 18, true); // Age 18 in 2026
    assert.strictEqual(calculateAge("2009-01-01") >= 18, false); // Age 17 in 2026

    console.log("\x1b[32m%s\x1b[0m", "ALL TESTS PASSED SUCCESSFULLY!");
} catch (err) {
    console.error("Test failed: ", err);
    process.exit(1);
}
