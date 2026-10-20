<?php
require_once "db.php";

session_start();
$error = "";
$success = "";
$email = "";

if (isset($_GET["logout"])) {
    $success = "You have been safely logged out.";
}

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];
                $_SESSION["user_email"] = $user["email"];

                $updateLogin = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateLogin->execute([$user["id"]]);

                header("Location: users.php");
                exit;
            } else {
                $error = "Invalid email or password. Please verify your credentials.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Login (Practical 9)</title>
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
    <h2>Portal Authentication</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 9: Verifying credentials with password_verify()</p>

    <?php if (!empty($success)): ?>
        <div class="alert-success">
            <strong>Notice:</strong> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-danger">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" style="max-width:450px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Password:</label>
            <input type="password" name="password" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Log In</button>
            <a href="register.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">Register New User</a>
        </div>

        <div style="margin-top:25px; padding:15px; background:#f4f7fb; border:1px solid #dbe3ee; border-radius:4px; font-size:13px;">
            <strong>Demo Accounts:</strong><br>
            Admin: <code>admin@studenthub.com</code> / <code>Admin@123</code><br>
            Student: <code>student@studenthub.com</code> / <code>Student@123</code>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
