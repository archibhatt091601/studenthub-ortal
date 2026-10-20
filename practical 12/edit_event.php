<?php
require_once "db.php";

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    header("Location: events.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$ev = $stmt->fetch();
if (!$ev) {
    die("Event not found.");
}

$title = $ev["title"];
$category = $ev["category"];
$eventDate = $ev["event_date"];
$venue = $ev["venue"];
$description = $ev["description"];
$status = $ev["status"];
$existingPoster = $ev["poster_image"];
$errors = [];

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "Workshop");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = trim($_POST["status"] ?? "upcoming");

    if (empty($title) || strlen($title) < 3) {
        $errors[] = "Event title must be at least 3 characters.";
    }
    if (empty($eventDate)) {
        $errors[] = "Event date is required.";
    }

    $posterFilename = $existingPoster;
    if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES["poster"]["tmp_name"];
        $fileName = $_FILES["poster"]["name"];
        $fileSize = $_FILES["poster"]["size"];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = ["jpg", "jpeg", "png", "webp", "svg"];
        if (!in_array($ext, $allowedExts, true)) {
            $errors[] = "Only JPG, PNG, WEBP, and SVG image files are allowed.";
        } elseif ($fileSize > 2 * 1024 * 1024) {
            $errors[] = "Poster image size must not exceed 2MB.";
        } else {
            $newFilename = uniqid("poster_") . "." . $ext;
            if (move_uploaded_file($fileTmp, __DIR__ . "/uploads/" . $newFilename)) {
                if (!empty($existingPoster) && file_exists(__DIR__ . "/uploads/" . $existingPoster)) {
                    unlink(__DIR__ . "/uploads/" . $existingPoster);
                }
                $posterFilename = $newFilename;
            } else {
                $errors[] = "Failed to upload new poster file.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $upd = $pdo->prepare("UPDATE events SET title = ?, category = ?, event_date = ?, venue = ?, description = ?, status = ?, poster_image = ? WHERE id = ?");
            $upd->execute([$title, $category, $eventDate, $venue, $description, $status, $posterFilename, $id]);
            header("Location: events.php?msg=updated");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Edit Event (Practical 12)</title>
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
    <h2>Edit Event & Poster (ID: #<?php echo $id; ?>)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 12: Updating event details and replacing poster file</p>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <ul style="margin:0 0 0 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_event.php?id=<?php echo $id; ?>" enctype="multipart/form-data" style="max-width:550px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Event Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Category:</label>
            <select name="category" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;">
                <option value="Workshop" <?php if ($category === "Workshop") echo "selected"; ?>>Workshop</option>
                <option value="Competition" <?php if ($category === "Competition") echo "selected"; ?>>Competition</option>
                <option value="Seminar" <?php if ($category === "Seminar") echo "selected"; ?>>Seminar</option>
                <option value="Festival" <?php if ($category === "Festival") echo "selected"; ?>>Festival</option>
                <option value="Sports" <?php if ($category === "Sports") echo "selected"; ?>>Sports</option>
            </select>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Event Date:</label>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($eventDate); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Venue:</label>
            <input type="text" name="venue" value="<?php echo htmlspecialchars($venue); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <?php if (!empty($existingPoster)): ?>
            <div style="margin-bottom:15px; text-align:center;">
                <p style="font-weight:bold; margin-bottom:5px;">Current Poster:</p>
                <img src="uploads/<?php echo htmlspecialchars($existingPoster); ?>" alt="Poster" style="max-width:180px; max-height:120px; border-radius:4px; border:1px solid #ccc;">
            </div>
        <?php endif; ?>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Replace Poster (Optional):</label>
            <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,.svg" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px; background:#fff;">
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Description:</label>
            <textarea name="description" rows="3" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Status:</label>
            <select name="status" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;">
                <option value="upcoming" <?php if ($status === "upcoming") echo "selected"; ?>>Upcoming</option>
                <option value="completed" <?php if ($status === "completed") echo "selected"; ?>>Completed</option>
                <option value="cancelled" <?php if ($status === "cancelled") echo "selected"; ?>>Cancelled</option>
            </select>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Save Changes</button>
            <a href="events.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">Back to Events</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
