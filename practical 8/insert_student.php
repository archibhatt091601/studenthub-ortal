<?php
require_once "db.php";

$success = "";
$error = "";

$students = [];
$events = [];

try {
    $students = $pdo->query("SELECT id, name, course, year FROM students ORDER BY name ASC")->fetchAll();
    $events = $pdo->query("SELECT id, title, event_date, venue FROM events ORDER BY event_date ASC")->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading data: " . $e->getMessage();
}

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $studentId = intval($_POST["student_id"] ?? 0);
    $eventId = intval($_POST["event_id"] ?? 0);

    if ($studentId <= 0 || $eventId <= 0) {
        $error = "Please select both a student and an event.";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM registrations WHERE student_id = ? AND event_id = ?");
            $check->execute([$studentId, $eventId]);
            if ($check->fetch()) {
                $error = "This student is already registered for this event.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO registrations (student_id, event_id, status) VALUES (?, ?, 'confirmed')");
                $stmt->execute([$studentId, $eventId]);
                $success = "Student has been successfully registered for the event using PDO Prepared Statements!";
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
    <title>StudentHub | Event Registration (Practical 8)</title>
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
    <h2>Event Registration (Prepared Statement)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 8: Secure database insertion using PDO prepared statement</p>

    <?php if (!empty($success)): ?>
        <div class="alert-success">
            <strong>Success:</strong> <?php echo $success; ?>
            <div style="margin-top:10px;">
                <a href="students_event.php" class="btn btn-primary" style="margin-right:10px;">View Registrations</a>
                <a href="insert_student.php" class="btn btn-warning">Register Another</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-danger">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="insert_student.php" style="max-width:550px; margin:0 auto;">
        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Select Student:</label>
            <select name="student_id" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="">-- Choose Student --</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?php echo $s["id"]; ?>">
                        <?php echo htmlspecialchars($s["name"] . " (" . $s["course"] . " - " . $s["year"] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:25px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Select Event:</label>
            <select name="event_id" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
                <option value="">-- Choose Event --</option>
                <?php foreach ($events as $ev): ?>
                    <option value="<?php echo $ev["id"]; ?>">
                        <?php echo htmlspecialchars($ev["title"] . " (" . $ev["event_date"] . " @ " . $ev["venue"] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Confirm Event Registration</button>
            <a href="students_event.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">View Registrations</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
