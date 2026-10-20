<?php
require_once "db.php";

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: students.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student record not found.");
}

$name = $student["name"];
$email = $student["email"];
$mobile = $student["mobile"];
$course = $student["course"];
$year = $student["year"];
$gender = $student["gender"];
$status = $student["status"];
$errors = [];

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $mobile = trim($_POST["mobile"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "Male");
    $status = trim($_POST["status"] ?? "active");

    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }

    if (empty($errors)) {
        try {
            $upd = $pdo->prepare("UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ?, status = ? WHERE id = ?");
            $upd->execute([$name, $email, $mobile, $course, $year, $gender, $status, $id]);
            header("Location: students.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Edit Student (Practical 11)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="students.php" class="m3">Manage Students</a>
<a href="add_student.php" class="m2">Add Student</a>
<a href="export_csv.php" class="m4">Export CSV</a>
<a href="dashboard.html" class="m5">Dashboard</a>
<a href="events.html" class="m6">Events</a>
<a href="profile.html" class="m7">Profile</a>
<a href="contact.html" class="m8">Contact</a>
<a href="faq.html" class="m9">FAQ</a>
    </nav>
</header>

<div class="page-box">
    <h2>Edit Student Profile (ID: #<?php echo $id; ?>)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 11: Updating student record using prepared statement</p>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <ul style="margin: 0 0 0 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_student.php?id=<?php echo $id; ?>" style="max-width:550px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Mobile Number:</label>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Course:</label>
            <select name="course" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="Computer Engineering" <?php if ($course === "Computer Engineering") echo "selected"; ?>>Computer Engineering</option>
                <option value="Information Technology" <?php if ($course === "Information Technology") echo "selected"; ?>>Information Technology</option>
                <option value="Civil Engineering" <?php if ($course === "Civil Engineering") echo "selected"; ?>>Civil Engineering</option>
                <option value="Mechanical Engineering" <?php if ($course === "Mechanical Engineering") echo "selected"; ?>>Mechanical Engineering</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Year:</label>
            <select name="year" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="First Year" <?php if ($year === "First Year") echo "selected"; ?>>First Year</option>
                <option value="Second Year" <?php if ($year === "Second Year") echo "selected"; ?>>Second Year</option>
                <option value="Third Year" <?php if ($year === "Third Year") echo "selected"; ?>>Third Year</option>
                <option value="Fourth Year" <?php if ($year === "Fourth Year") echo "selected"; ?>>Fourth Year</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Gender:</label>
            <label style="margin-right:15px;"><input type="radio" name="gender" value="Male" <?php if ($gender === "Male") echo "checked"; ?>> Male</label>
            <label style="margin-right:15px;"><input type="radio" name="gender" value="Female" <?php if ($gender === "Female") echo "checked"; ?>> Female</label>
            <label><input type="radio" name="gender" value="Other" <?php if ($gender === "Other") echo "checked"; ?>> Other</label>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Status:</label>
            <select name="status" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;">
                <option value="active" <?php if ($status === "active") echo "selected"; ?>>Active</option>
                <option value="inactive" <?php if ($status === "inactive") echo "selected"; ?>>Inactive (Soft Deleted)</option>
            </select>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Update Record</button>
            <a href="students.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">Back to List</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
