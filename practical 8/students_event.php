<?php
require_once "db.php";

$registrations = [];
$error = "";

try {
    $sql = "SELECT r.id, s.name AS student_name, s.email, s.course, s.year, e.title AS event_title, e.event_date, e.venue, r.registration_date, r.status
            FROM registrations r
            JOIN students s ON r.student_id = s.id
            JOIN events e ON r.event_id = e.id
            ORDER BY r.registration_date DESC";
    $registrations = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $error = "Error querying registrations: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Joined Event Registrations (Practical 8)</title>
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
    <h2>Registered Students & Events (SQL JOIN)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 8: Querying and displaying registered students with event details via SQL JOIN</p>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3>All Confirmed Event Registrations (Total: <?php echo count($registrations); ?>)</h3>
        <a href="insert_student.php" class="btn btn-primary">+ Register Another Student</a>
    </div>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>Reg #</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Course & Year</th>
                    <th>Event Title</th>
                    <th>Event Date</th>
                    <th>Venue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registrations)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:#888;">No event registrations found in database.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registrations as $r): ?>
                        <tr>
                            <td><?php echo $r["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($r["student_name"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($r["email"]); ?></td>
                            <td><?php echo htmlspecialchars($r["course"] . " - " . $r["year"]); ?></td>
                            <td><strong><?php echo htmlspecialchars($r["event_title"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($r["event_date"]); ?></td>
                            <td><?php echo htmlspecialchars($r["venue"]); ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <?php echo htmlspecialchars(ucfirst($r["status"])); ?>
                                </span>
                            </td>
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
