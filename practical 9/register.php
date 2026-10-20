<?php
require_once "db.php";

$name = "";
$email = "";
$role = "student";
$errors = [];
$success = "";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    $role = trim($_POST["role"] ?? "student");

    if (empty($name) || strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid email address.";
    }

    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Password confirmation does not match.";
    }

    if (!in_array($role, ["student", "admin"], true)) {
        $role = "student";
    }

    if (empty($errors)) {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors[] = "A user with this email address is already registered.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $hashedPassword, $role]);
                $success = "Registration completed successfully with secure password hashing! You can now log in.";
                $name = "";
                $email = "";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Secure Registration (Practical 9)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="register.php" class="m2">Register</a>
<a href="login.php" class="m9">Login</a>
<a href="users.php" class="m3">Users List</a>
<a href="dashboard.html" class="m4">Dashboard</a>
<a href="events.html" class="m5">Events</a>
<a href="profile.html" class="m6">Profile</a>
<a href="contact.html" class="m7">Contact</a>
<a href="faq.html" class="m8">FAQ</a>
    </nav>
</header>

<div class="page-box">
    <h2>Secure User Registration</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 9: Authentication with password_hash() and Prepared Statements</p>

    <?php if (!empty($success)): ?>
        <div class="alert-success">
            <strong>Success:</strong> <?php echo $success; ?>
            <div style="margin-top:10px;">
                <a href="login.php" class="btn btn-primary" style="margin-right:10px;">Go to Login</a>
                <a href="users.php" class="btn btn-warning">View Registered Users</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <strong>Please correct the following:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php" style="max-width:550px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Password (Min 6 Characters):</label>
            <input type="password" name="password" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Confirm Password:</label>
            <input type="password" name="confirm_password" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Select Role:</label>
            <select name="role" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;">
                <option value="student" <?php if ($role === "student") echo "selected"; ?>>Student</option>
                <option value="admin" <?php if ($role === "admin") echo "selected"; ?>>Admin</option>
            </select>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Create Account</button>
            <a href="login.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">Already have an account? Login</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
