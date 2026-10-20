<?php
$fullName = "";
$email = "";
$subject = "";
$message = "";
$errors = [];
$success = "";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    $fullName = trim($_POST["fullName"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    $cleanName = htmlspecialchars($fullName, ENT_QUOTES, "UTF-8");
    $cleanEmail = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
    $cleanSubject = htmlspecialchars($subject, ENT_QUOTES, "UTF-8");
    $cleanMessage = htmlspecialchars($message, ENT_QUOTES, "UTF-8");

    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }
    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    if (empty($errors)) {
        $submittedAt = date("Y-m-d H:i:s");
        $csvFile = __DIR__ . "/contact.csv";
        $csvExists = file_exists($csvFile) && filesize($csvFile) > 0;
        $fp = fopen($csvFile, "a");
        if ($fp) {
            if (!$csvExists) {
                fputcsv($fp, ["Full Name", "Email", "Subject", "Message", "Submitted At"]);
            }
            fputcsv($fp, [$cleanName, $cleanEmail, $cleanSubject, $cleanMessage, $submittedAt]);
            fclose($fp);
        }

        $jsonFile = __DIR__ . "/contact.json";
        $records = [];
        if (file_exists($jsonFile)) {
            $raw = file_get_contents($jsonFile);
            if (!empty($raw)) {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $records = $decoded;
                }
            }
        }
        $records[] = [
            "fullName" => $cleanName,
            "email" => $cleanEmail,
            "subject" => $cleanSubject,
            "message" => $cleanMessage,
            "submittedAt" => $submittedAt
        ];
        file_put_contents($jsonFile, json_encode($records, JSON_PRETTY_PRINT), LOCK_EX);

        $success = "Thank you! Your message has been saved successfully.";
        $fullName = "";
        $email = "";
        $subject = "";
        $message = "";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Contact Us (Practical 7)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="register.php" class="m2">Register</a>
<a href="login.html" class="m9">Login</a>
<a href="dashboard.html" class="m3">Dashboard</a>
<a href="events.html" class="m4">Events</a>
<a href="profile.html" class="m5">Profile</a>
<a href="contact.php" class="m6">Contact</a>
<a href="records.php" class="m7">View Records</a>
<a href="faq.html" class="m8">FAQ</a>
<a href="feedback.html" class="m8">Feedback</a>
    </nav>
</header>

<div class="page-box">
    <h2>Contact Us</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Send us your query or feedback. Submissions are saved to CSV & JSON.</p>

    <?php if (!empty($success)): ?>
        <div class="alert-success">
            <strong>Success:</strong> <?php echo $success; ?>
            <div style="margin-top:10px;">
                <a href="records.php" class="btn btn-primary">View Saved Inquiries</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <strong>Please resolve the following errors:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo $err; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="contact.php" style="max-width:600px; margin:0 auto;">
        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Full Name:</label>
            <input type="text" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Email Address:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:15px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Subject:</label>
            <input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; font-weight:bold; margin-bottom:5px;">Message:</label>
            <textarea name="message" rows="4" style="width:100%; padding:9px; border:1px solid #ccc; border-radius:4px;" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>

        <div style="text-align:center;">
            <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:16px;">Send Message</button>
            <a href="records.php" class="btn btn-warning" style="padding:10px 24px; font-size:16px; margin-left:10px;">View Records</a>
        </div>
    </form>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
