<?php
require_once "db.php";

session_start();

$isLoggedIn = isset($_SESSION["user_id"]);
$userName = $_SESSION["user_name"] ?? "";
$userRole = $_SESSION["user_role"] ?? "";

$users = [];
$error = "";

try {
    $users = $pdo->query("SELECT id, name, email, role, created_at, last_login FROM users ORDER BY id DESC")->fetchAll();
} catch (PDOException $e) {
    $error = "Error querying users: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Registered Users (Practical 9)</title>
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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <h2>Registered Users</h2>
        <div>
            <?php if ($isLoggedIn): ?>
                <span style="margin-right:15px;">Logged in as: <strong><?php echo htmlspecialchars($userName); ?></strong> (<?php echo htmlspecialchars(ucfirst($userRole)); ?>)</span>
                <a href="logout.php" class="btn btn-warning">Log Out</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary" style="margin-right:10px;">Log In</a>
                <a href="register.php" class="btn btn-warning">+ Register New User</a>
            <?php endif; ?>
        </div>
    </div>
    <p style="color:#555; margin-bottom:20px;">Practical 9: Displaying users stored with Bcrypt password hashing. Passwords are never stored in plain text.</p>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Registered At</th>
                    <th>Last Login</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px; color:#888;">No registered users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo $u["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($u["name"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($u["email"]); ?></td>
                            <td>
                                <span class="badge <?php echo ($u["role"] === "admin") ? "badge-danger" : "badge-success"; ?>">
                                    <?php echo htmlspecialchars(ucfirst($u["role"])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($u["created_at"] ?? "N/A"); ?></td>
                            <td><?php echo htmlspecialchars($u["last_login"] ?? "Never"); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
