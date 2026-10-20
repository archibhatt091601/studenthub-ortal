<?php
$fullName = "";
$email = "";
$mobile = "";
$course = "";
$year = "";
$gender = "";
$message = "";
$errors = [];
$success = "";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $message = trim($_POST["message"] ?? "");

    $cleanName = htmlspecialchars($fullName, ENT_QUOTES, "UTF-8");
    $cleanEmail = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
    $cleanMobile = htmlspecialchars($mobile, ENT_QUOTES, "UTF-8");
    $cleanCourse = htmlspecialchars($course, ENT_QUOTES, "UTF-8");
    $cleanYear = htmlspecialchars($year, ENT_QUOTES, "UTF-8");
    $cleanGender = htmlspecialchars($gender, ENT_QUOTES, "UTF-8");
    $cleanMessage = htmlspecialchars($message, ENT_QUOTES, "UTF-8");

    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    } elseif (strlen($fullName) < 3) {
        $errors[] = "Full name must be at least 3 characters.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }

    if (empty($mobile)) {
        $errors[] = "Mobile number is required.";
    } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must be 10 digits.";
    }

    $validCourses = ["Computer Engineering", "Information Technology", "Civil Engineering", "Mechanical Engineering", "bca", "bba", "bcom", "bsc", "other"];
    if (empty($course)) {
        $errors[] = "Course selection is required.";
    }

    $validYears = ["First Year", "Second Year", "Third Year", "Fourth Year", "1", "2", "3", "4", "other"];
    if (empty($year)) {
        $errors[] = "Year selection is required.";
    }

    if (empty($gender)) {
        $errors[] = "Gender selection is required.";
    }

    if (empty($message)) {
        $errors[] = "Message is required.";
    } elseif (strlen($message) < 5) {
        $errors[] = "Message must be at least 5 characters.";
    }

    if (empty($errors)) {
        $submittedAt = date("Y-m-d H:i:s");
        $csvFile = __DIR__ . "/registrations.csv";
        $csvExists = file_exists($csvFile) && filesize($csvFile) > 0;
        $fp = fopen($csvFile, "a");
        if ($fp) {
            if (!$csvExists) {
                fputcsv($fp, ["Full Name", "Email", "Mobile", "Course", "Year", "Gender", "Message", "Submitted At"]);
            }
            fputcsv($fp, [$cleanName, $cleanEmail, $cleanMobile, $cleanCourse, $cleanYear, $cleanGender, $cleanMessage, $submittedAt]);
            fclose($fp);
        }

        $jsonFile = __DIR__ . "/registrations.json";
        $records = [];
        if (file_exists($jsonFile)) {
            $raw = file_get_contents($jsonFile);
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $records = $decoded;
                }
            }
        }
        $records[] = [
            "fullName" => $cleanName,
            "email" => $cleanEmail,
            "mobile" => $cleanMobile,
            "course" => $cleanCourse,
            "year" => $cleanYear,
            "gender" => $cleanGender,
            "message" => $cleanMessage,
            "submittedAt" => $submittedAt
        ];
        file_put_contents($jsonFile, json_encode($records, JSON_PRETTY_PRINT), LOCK_EX);

        $success = "Registration successful! Record has been saved to CSV and JSON storage.";
        $fullName = "";
        $email = "";
        $mobile = "";
        $course = "";
        $year = "";
        $gender = "";
        $message = "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Register (Practical 7)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="register.php" class="m2">Register</a>
<a href="login.html" class="m9">Login</a>
<a href="dashboard.html" class="m3">Dashboard</a>
<a href="events.html" class="m4">Events</a>
<a href="profile.html" class="m5">Profile</a>
<a href="contact.php" class="m6">Contact</a>
<a href="records.php" class="m7">View Records</a>
<a href="faq.html" class="m8">FAQ</a>
<a href="feedback.html" class="m8">Feedback</a>
    </nav>
</header>

<div class="page-box">
    <h2>Student Registration Form</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 7: Form Handling, Validation, CSV & JSON Storage</p>

    <?php if (!empty($success)): ?>
        <div class="alert-success">
            <strong>Success:</strong> <?php echo $success; ?>
            <div style="margin-top:10px;">
                <a href="records.php" class="btn btn-primary" style="margin-right:10px;">View Stored Records</a>
                <a href="register.php" class="btn btn-warning">Register Another</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <strong>Please resolve the following errors:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo $err; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" style="max-width:600px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Full Name:</label>
            <input type="text" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Mobile Number (10 Digits):</label>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Course:</label>
            <select name="course" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="">Select Course</option>
                <option value="Computer Engineering" <?php if ($course === "Computer Engineering") echo "selected"; ?>>Computer Engineering</option>
                <option value="Information Technology" <?php if ($course === "Information Technology") echo "selected"; ?>>Information Technology</option>
                <option value="Civil Engineering" <?php if ($course === "Civil Engineering") echo "selected"; ?>>Civil Engineering</option>
                <option value="Mechanical Engineering" <?php if ($course === "Mechanical Engineering") echo "selected"; ?>>Mechanical Engineering</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Year:</label>
            <select name="year" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="">Select Year</option>
                <option value="First Year" <?php if ($year === "First Year") echo "selected"; ?>>First Year</option>
                <option value="Second Year" <?php if ($year === "Second Year") echo "selected"; ?>>Second Year</option>
                <option value="Third Year" <?php if ($year === "Third Year") echo "selected"; ?>>Third Year</option>
                <option value="Fourth Year" <?php if ($year === "Fourth Year") echo "selected"; ?>>Fourth Year</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Gender:</label>
            <label style="margin-right:15px;"><input type="radio" name="gender" value="Male" <?php if ($gender === "Male" || empty($gender)) echo "checked"; ?>> Male</label>
            <label style="margin-right:15px;"><input type="radio" name="gender" value="Female" <?php if ($gender === "Female") echo "checked"; ?>> Female</label>
            <label><input type="radio" name="gender" value="Other" <?php if ($gender === "Other") echo "checked"; ?>> Other</label>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Message / Remarks:</label>
            <textarea name="message" rows="3" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Submit Registration</button>
            <a href="records.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">View Records</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
