<?php
require_once "db.php";
require_once "auth.php";

require_login();

$userName = $_SESSION["user_name"] ?? "Student";
$userEmail = $_SESSION["user_email"] ?? "";
$userRole = $_SESSION["user_role"] ?? "student";
$accessNotice = "";

if (isset($_GET["access_denied"])) {
    $accessNotice = "Access denied: The Admin Dashboard requires administrator privileges.";
}

$myRegistrations = [];
try {
    $stmt = $pdo->prepare("SELECT r.id, e.title, e.event_date, e.venue, r.registration_date, r.status
                           FROM registrations r
                           JOIN events e ON r.event_id = e.id
                           ORDER BY r.registration_date DESC LIMIT 5");
    $stmt->execute();
    $myRegistrations = $stmt->fetchAll();
} catch (PDOException $e) {
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Student Dashboard (Practical 10)</title>
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
    <?php if (!empty($accessNotice)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($accessNotice); ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2>Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
        <div>
            <span class="badge badge-success" style="font-size:14px; padding:6px 12px; margin-right:10px;">Role: <?php echo htmlspecialchars(ucfirst($userRole)); ?></span>
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </div>
    </div>

    <div style="background:#e8f5e9; border:1px solid #c8e6c9; padding:15px; border-radius:6px; margin-bottom:25px;">
        <p><strong>Session Active:</strong> Logged in as <code><?php echo htmlspecialchars($userEmail); ?></code>.</p>
        <p style="font-size:13px; color:#555; margin-top:5px;">Your session will automatically expire after 15 minutes of inactivity as required by Practical 10.</p>
    </div>

    <h3>Your Enrolled Events</h3>
    <div class="table-container" style="margin-top:15px;">
        <table class="record-table">
            <thead>
                <tr>
                    <th>Reg ID</th>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($myRegistrations)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:20px; color:#888;">No event enrollments found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($myRegistrations as $r): ?>
                        <tr>
                            <td><?php echo $r["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($r["title"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($r["event_date"]); ?></td>
                            <td><?php echo htmlspecialchars($r["venue"]); ?></td>
                            <td><span class="badge badge-success"><?php echo htmlspecialchars(ucfirst($r["status"])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="text-align:center; margin-top:30px;">
        <a href="events.html" class="btn btn-primary" style="margin-right:10px;">Browse More Events</a>
        <?php if ($userRole === "admin"): ?>
            <a href="admin_dashboard.php" class="btn btn-warning">Go to Admin Dashboard</a>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
