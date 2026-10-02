
// Registration Form Validation

(function () {
const form = document.getElementById("registrationForm");

if (form) {
form.addEventListener("submit", function (event) {

    event.preventDefault();

    // Get values
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const mobile = document.getElementById("mobile").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const course = document.getElementById("course").value;
    const year = document.getElementById("year").value;

    const gender = document.querySelector(
        'input[name="gender"]:checked'
    );

    const terms = document.getElementById("terms").checked;

    // Error elements
    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const mobileError = document.getElementById("mobileError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError =
        document.getElementById("confirmPasswordError");
    const courseError = document.getElementById("courseError");
    const yearError = document.getElementById("yearError");
    const genderError = document.getElementById("genderError");
    const termsError = document.getElementById("termsError");

    // Clear previous errors
    nameError.textContent = "";
    emailError.textContent = "";
    mobileError.textContent = "";
    passwordError.textContent = "";
    confirmPasswordError.textContent = "";
    courseError.textContent = "";
    yearError.textContent = "";
    genderError.textContent = "";
    termsError.textContent = "";

    let valid = true;

    // Regular Expressions
    const namePattern = /^[A-Za-z ]{2,50}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const mobilePattern = /^[6-9][0-9]{9}$/;

    // Name validation
    if (name === "") {
        nameError.textContent = "Name is required.";
        valid = false;
    }
    else if (!namePattern.test(name)) {
        nameError.textContent =
            "Enter a valid name using only letters and spaces.";
        valid = false;
    }

    // Email validation
    if (email === "") {
        emailError.textContent = "Email is required.";
        valid = false;
    }
    else if (!emailPattern.test(email)) {
        emailError.textContent = "Enter a valid email address.";
        valid = false;
    }

    // Mobile validation
    if (mobile === "") {
        mobileError.textContent = "Mobile number is required.";
        valid = false;
    }
    else if (!mobilePattern.test(mobile)) {
        mobileError.textContent =
            "Enter a valid 10-digit Indian mobile number.";
        valid = false;
    }

    // Password validation
    if (password === "") {
        passwordError.textContent = "Password is required.";
        valid = false;
    }
    else if (password.length < 8) {
        passwordError.textContent =
            "Password must contain at least 8 characters.";
        valid = false;
    }
    else if (!/[A-Z]/.test(password)) {
        passwordError.textContent =
            "Password must contain at least one uppercase letter.";
        valid = false;
    }
    else if (!/[a-z]/.test(password)) {
        passwordError.textContent =
            "Password must contain at least one lowercase letter.";
        valid = false;
    }
    else if (!/[0-9]/.test(password)) {
        passwordError.textContent =
            "Password must contain at least one number.";
        valid = false;
    }
    else if (!/[!@#$%^&*]/.test(password)) {
        passwordError.textContent =
            "Password must contain at least one special character.";
        valid = false;
    }

    // Confirm password validation
    if (confirmPassword === "") {
        confirmPasswordError.textContent =
            "Please confirm your password.";
        valid = false;
    }
    else if (password !== confirmPassword) {
        confirmPasswordError.textContent =
            "Passwords do not match.";
        valid = false;
    }

    // Course validation
    if (course === "") {
        courseError.textContent = "Please select a course.";
        valid = false;
    }

    // Year validation
    if (year === "") {
        yearError.textContent = "Please select your year.";
        valid = false;
    }

    // Gender validation
    if (!gender) {
        genderError.textContent = "Please select your gender.";
        valid = false;
    }

    // Terms validation
    if (!terms) {
        termsError.textContent =
            "You must accept the terms and conditions.";
        valid = false;
    }

    // Final result
    if (valid) {
        alert("Registration successful!");
        form.reset();

        // Reset password strength meter
        const strength = document.getElementById("passwordStrength");
        if (strength) {
            strength.textContent = "";
        }
    }
});


// Password Strength Meter
const passwordInput = document.getElementById("password");
const passwordStrength =
    document.getElementById("passwordStrength");

passwordInput.addEventListener("input", function () {

    const password = passwordInput.value;

    if (password.length === 0) {
        passwordStrength.textContent = "";
        return;
    }

    let score = 0;

    if (password.length >= 8)
        score++;

    if (/[A-Z]/.test(password))
        score++;

    if (/[a-z]/.test(password))
        score++;

    if (/[0-9]/.test(password))
        score++;

    if (/[!@#$%^&*]/.test(password))
        score++;

    if (score <= 2) {
        passwordStrength.textContent = "Weak password";
    }
    else if (score <= 4) {
        passwordStrength.textContent = "Medium password";
    }
    else {
        passwordStrength.textContent = "Strong password";
    }
});


// Real-time validation
passwordInput.addEventListener("keyup", function () {

    if (passwordInput.value.length < 8) {
        passwordStrength.textContent =
            "Password should contain at least 8 characters.";
    }
});
}
})();
