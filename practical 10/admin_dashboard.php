<?php
require_once "db.php";
require_once "auth.php";

require_admin();

$userName = $_SESSION["user_name"] ?? "Admin";
$userEmail = $_SESSION["user_email"] ?? "";

$totalUsers = 0;
$totalStudents = 0;
$totalEvents = 0;
$usersList = [];

try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $usersList = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC LIMIT 10")->fetchAll();
} catch (PDOException $e) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Admin Dashboard (Practical 10)</title>
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
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2>Administrator Executive Panel</h2>
        <div>
            <span class="badge badge-danger" style="font-size:14px; padding:6px 12px; margin-right:10px;">Role: Admin</span>
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <h3>Registered Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-card">
            <h3>Student Profiles</h3>
            <p><?php echo $totalStudents; ?></p>
        </div>
        <div class="stat-card">
            <h3>Active Events</h3>
            <p><?php echo $totalEvents; ?></p>
        </div>
        <div class="stat-card">
            <h3>Session Timeout</h3>
            <p style="font-size:22px;">15 Min</p>
        </div>
    </div>

    <h3 style="margin-top:30px; margin-bottom:15px;">User Accounts & Roles</h3>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersList as $u): ?>
                    <tr>
                        <td><?php echo $u["id"]; ?></td>
                        <td><strong><?php echo htmlspecialchars($u["name"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($u["email"]); ?></td>
                        <td>
                            <span class="badge <?php echo ($u["role"] === "admin") ? "badge-danger" : "badge-success"; ?>">
                                <?php echo htmlspecialchars(ucfirst($u["role"])); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align:center; margin-top:30px;">
        <a href="student_dashboard.php" class="btn btn-primary" style="margin-right:10px;">View Student Dashboard</a>
        <a href="logout.php" class="btn btn-warning">Log Out</a>
    </div>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
