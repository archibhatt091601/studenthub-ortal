<?php
require_once "db.php";
require_once "auth.php";

$error = "";
$info = "";

if (isset($_GET["timeout"])) {
    $info = "Your session expired after 15 minutes of inactivity. Please log in again.";
} elseif (isset($_GET["auth_required"])) {
    $info = "Please log in to access protected dashboard pages.";
} elseif (isset($_GET["logout"])) {
    $info = "You have been safely logged out.";
}

$savedEmail = $_COOKIE["remember_email"] ?? "";
$email = $savedEmail;

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember_me"]);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all credentials.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];
                $_SESSION["last_activity"] = time();

                if ($remember) {
                    setcookie("remember_email", $email, time() + (86400 * 30), "/");
                } else {
                    setcookie("remember_email", "", time() - 3600, "/");
                }

                if ($user["role"] === "admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid email or password combination.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Session Login (Practical 10)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="login.php" class="m9">Login</a>
<a href="student_dashboard.php" class="m3">Student Dashboard</a>
<a href="admin_dashboard.php" class="m4">Admin Dashboard</a>
<a href="logout.php" class="m2">Logout</a>
<a href="events.html" class="m5">Events</a>
<a href="profile.html" class="m6">Profile</a>
<a href="contact.html" class="m7">Contact</a>
<a href="faq.html" class="m8">FAQ</a>
    </nav>
</header>

<div class="page-box">
    <h2>Authentication & Session Management</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 10: Role-based access control, session timeouts & cookies</p>

    <?php if (!empty($info)): ?>
        <div class="alert-success" style="background:#e3f2fd; color:#0d47a1; border-color:#bbdefb;">
            <?php echo htmlspecialchars($info); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-danger">
            <strong>Login Failed:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php" style="max-width:460px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Password:</label>
            <input type="password" name="password" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="remember_me" value="1" <?php if (!empty($savedEmail)) echo "checked"; ?> style="margin-right:8px;">
                Remember me on this browser (Cookie for 30 days)
            </label>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Secure Login</button>
        </div>

        <div style="margin-top:25px; padding:15px; background:#f4f7fb; border:1px solid #dbe3ee; border-radius:4px; font-size:13px;">
            <strong>Test Accounts:</strong><br>
            Administrator: <code>admin@studenthub.com</code> / <code>Admin@123</code> &rarr; <em>Accesses Admin Dashboard</em><br>
            Student: <code>student@studenthub.com</code> / <code>Student@123</code> &rarr; <em>Accesses Student Dashboard</em>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
