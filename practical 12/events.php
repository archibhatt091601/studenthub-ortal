<?php
require_once "db.php";

$events = [];
$error = "";
$msg = $_GET["msg"] ?? "";

try {
    $events = $pdo->query("SELECT id, title, category, event_date, venue, description, status, poster_image FROM events ORDER BY event_date ASC")->fetchAll();
} catch (PDOException $e) {
    $error = "Error querying events: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Event Listings & Posters (Practical 12)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="events.php" class="m3">Manage Events</a>
<a href="add_event.php" class="m2">Add Event</a>
<a href="dashboard.html" class="m4">Dashboard</a>
<a href="profile.html" class="m5">Profile</a>
<a href="contact.html" class="m6">Contact</a>
<a href="faq.html" class="m7">FAQ</a>
<a href="feedback.html" class="m8">Feedback</a>
    </nav>
</header>

<div class="page-box">
    <h2>Event Listings & Poster Management</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 12: Image upload validation, unique renaming, secure storage & CRUD</p>

    <?php if ($msg === "added"): ?>
        <div class="alert-success">Event and poster image uploaded successfully!</div>
    <?php elseif ($msg === "updated"): ?>
        <div class="alert-success">Event details and poster updated successfully!</div>
    <?php elseif ($msg === "deleted"): ?>
        <div class="alert-success" style="background:#fff3e0; color:#ef6c00;">Event deleted and poster removed from disk.</div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <h3>All Campus Events (Total: <?php echo count($events); ?>)</h3>
        <a href="add_event.php" class="btn btn-primary">+ Add New Event</a>
    </div>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Event Title</th>
                    <th>Category</th>
                    <th>Event Date</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($events)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:#888;">No events listed yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                        <tr>
                            <td><?php echo $ev["id"]; ?></td>
                            <td style="text-align:center;">
                                <?php
                                $posterFile = $ev["poster_image"];
                                $posterSrc = "uploads/default.svg";
                                if (!empty($posterFile) && file_exists(__DIR__ . "/uploads/" . $posterFile)) {
                                    $posterSrc = "uploads/" . htmlspecialchars($posterFile);
                                }
                                ?>
                                <img src="<?php echo $posterSrc; ?>" alt="Poster" style="width:50px; height:50px; object-fit:cover; border-radius:4px; border:1px solid #dbe3ee;">
                            </td>
                            <td><strong><?php echo htmlspecialchars($ev["title"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($ev["category"]); ?></td>
                            <td><?php echo htmlspecialchars($ev["event_date"]); ?></td>
                            <td><?php echo htmlspecialchars($ev["venue"]); ?></td>
                            <td>
                                <span class="badge <?php echo ($ev["status"] === "upcoming") ? "badge-success" : (($ev["status"] === "completed") ? "badge-warning" : "badge-danger"); ?>">
                                    <?php echo htmlspecialchars(ucfirst($ev["status"])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_event.php?id=<?php echo $ev["id"]; ?>" class="btn-small" style="text-decoration:none; margin-right:5px;">Edit</a>
                                <a href="delete_event.php?id=<?php echo $ev["id"]; ?>" class="btn-small btn-danger" style="text-decoration:none;" onclick="return confirm('Delete this event and its poster image?');">Delete</a>
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
