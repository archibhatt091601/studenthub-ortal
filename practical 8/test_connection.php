<?php
require_once "db.php";

$dbStatus = "Connected";
$errorMsg = "";
$serverInfo = "";
$studentCount = 0;
$eventCount = 0;
$regCount = 0;

try {
    $serverInfo = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $regCount = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
} catch (PDOException $e) {
    $dbStatus = "Error";
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Test DB Connection (Practical 8)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="test_connection.php" class="m2">Test DB</a>
<a href="insert_student.php" class="m3">Event Register</a>
<a href="students_event.php" class="m4">Registrations</a>
<a href="dashboard.html" class="m5">Dashboard</a>
<a href="events.html" class="m6">Events</a>
<a href="profile.html" class="m7">Profile</a>
<a href="contact.html" class="m8">Contact</a>
<a href="faq.html" class="m9">FAQ</a>
    </nav>
</header>

<div class="page-box">
    <h2>Database Connection Status</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 8: Connecting PHP to MySQL using PDO and Prepared Statements</p>

    <?php if ($dbStatus === "Connected"): ?>
        <div class="alert-success">
            <strong>Connection Established:</strong> Successfully connected to MySQL database <code>studenthub</code> at <code>localhost</code>.
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <h3>MySQL Version</h3>
                <p style="font-size:20px;"><?php echo htmlspecialchars($serverInfo); ?></p>
            </div>
            <div class="stat-card">
                <h3>Students</h3>
                <p><?php echo $studentCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Events</h3>
                <p><?php echo $eventCount; ?></p>
            </div>
            <div class="stat-card">
                <h3>Registrations</h3>
                <p><?php echo $regCount; ?></p>
            </div>
        </div>

        <div style="text-align:center; margin-top:25px;">
            <a href="insert_student.php" class="btn btn-primary" style="margin-right:10px;">Register Student for Event</a>
            <a href="students_event.php" class="btn btn-warning">View Joined Event Registrations</a>
        </div>
    <?php else: ?>
        <div class="alert-danger">
            <strong>Connection Failed:</strong> <?php echo htmlspecialchars($errorMsg); ?>
            <p style="margin-top:10px;">Please ensure XAMPP MySQL is running and <code>studenthub.sql</code> has been imported.</p>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
