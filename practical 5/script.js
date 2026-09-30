const form = document.getElementById("registrationForm");
const password = document.getElementById("password");
const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

const nameRegex = /^[A-Za-z ]{2,50}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mobileRegex = /^[6-9]\d{9}$/;

function showError(id, message) {
    document.getElementById(id).textContent = message;
}

function clearErrors() {
    const errors = document.querySelectorAll(".error");
    errors.forEach(function(error) {
        error.textContent = "";
    });
    document.getElementById("successMessage").textContent = "";
}

function checkPasswordStrength(value) {
    let score = 0;

    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[a-z]/.test(value)) score++;
    if (/\d/.test(value)) score++;
    if (/[!@#$%^&*]/.test(value)) score++;

    if (value.length === 0) {
        strengthBar.style.width = "0%";
        strengthText.textContent = "Password strength: Not entered";
    } else if (score <= 2) {
        strengthBar.style.width = "35%";
        strengthText.textContent = "Password strength: Weak";
    } else if (score <= 4) {
        strengthBar.style.width = "70%";
        strengthText.textContent = "Password strength: Medium";
    } else {
        strengthBar.style.width = "100%";
        strengthText.textContent = "Password strength: Strong";
    }

    return score;
}

password.addEventListener("input", function() {
    checkPasswordStrength(password.value);
});

form.addEventListener("submit", function(event) {
    event.preventDefault();
    clearErrors();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const mobile = document.getElementById("mobile").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value;
    const course = document.getElementById("course").value;
    const year = document.querySelector('input[name="year"]:checked');
    const gender = document.querySelector('input[name="gender"]:checked');
    const terms = document.getElementById("terms").checked;

    let valid = true;

    if (!nameRegex.test(name)) {
        showError("nameError", "Enter a valid name using letters and spaces only.");
        valid = false;
    }

    if (!emailRegex.test(email)) {
        showError("emailError", "Enter a valid email address.");
        valid = false;
    }

    if (!mobileRegex.test(mobile)) {
        showError("mobileError", "Enter a valid 10-digit Indian mobile number.");
        valid = false;
    }

    const passwordScore = checkPasswordStrength(password.value);

    if (password.value.length < 8) {
        showError("passwordError", "Password must contain at least 8 characters.");
        valid = false;
    } else if (passwordScore < 4) {
        showError("passwordError", "Use uppercase, lowercase, number and special character.");
        valid = false;
    }

    if (password.value !== confirmPassword) {
        showError("confirmPasswordError", "Passwords do not match.");
        valid = false;
    }

    if (course === "") {
        showError("courseError", "Please select a course.");
        valid = false;
    }

    if (!year) {
        showError("yearError", "Please select your year.");
        valid = false;
    }

    if (!gender) {
        showError("genderError", "Please select your gender.");
        valid = false;
    }

    if (!terms) {
        showError("termsError", "You must accept the terms and conditions.");
        valid = false;
    }

    if (valid) {
        document.getElementById("successMessage").textContent =
            "Registration successful!";
        form.reset();
        checkPasswordStrength("");
    }
});
