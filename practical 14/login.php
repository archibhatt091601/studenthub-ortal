<?php
require_once "db.php";
session_start();

$error = "";
if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            $log = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, 'LOGIN', 'Admin authenticated into dashboard', ?)");
            $log->execute([$user["email"], $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);

            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Invalid administrator credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Admin Login (Practical 14)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "menu.php"; ?>

<div class="page-box">
    <h2>Administrative Login</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 14: Secure login for system administration</p>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" style="max-width:440px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Admin Email:</label>
            <input type="email" name="email" value="admin@studenthub.com" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>
        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Password:</label>
            <input type="password" name="password" value="Admin@123" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>
        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Log In to Admin Panel</button>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
