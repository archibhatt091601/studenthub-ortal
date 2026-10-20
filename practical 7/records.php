<?php
$csvFile = __DIR__ . "/registrations.csv";
$csvRows = [];
if (file_exists($csvFile)) {
    $fp = fopen($csvFile, "r");
    if ($fp) {
        $header = fgetcsv($fp);
        while (($data = fgetcsv($fp)) !== false) {
            if (!empty($data) && count($data) >= 7) {
                $csvRows[] = $data;
            }
        }
        fclose($fp);
    }
}

$jsonFile = __DIR__ . "/registrations.json";
$jsonRecords = [];
if (file_exists($jsonFile)) {
    $raw = file_get_contents($jsonFile);
    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $jsonRecords = $decoded;
        }
    }
}

$contactCsv = __DIR__ . "/contact.csv";
$contactRows = [];
if (file_exists($contactCsv)) {
    $fp = fopen($contactCsv, "r");
    if ($fp) {
        $header = fgetcsv($fp);
        while (($data = fgetcsv($fp)) !== false) {
            if (!empty($data) && count($data) >= 4) {
                $contactRows[] = $data;
            }
        }
        fclose($fp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Stored Records (Practical 7)</title>
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
    <h2>Stored Submissions (CSV & JSON)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 7: Submissions stored via PHP into filesystem CSV and JSON files.</p>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3>Student Registrations (from CSV file)</h3>
        <a href="register.php" class="btn btn-primary">+ Add New Registration</a>
    </div>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($csvRows)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:#888;">No CSV registration records found yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($csvRows as $idx => $r): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($r[0] ?? ""); ?></strong></td>
                            <td><?php echo htmlspecialchars($r[1] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($r[2] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($r[3] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($r[4] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($r[5] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($r[7] ?? $r[6] ?? ""); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 style="margin-top:40px; margin-bottom:15px;">Student Registrations (from JSON file)</h3>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jsonRecords)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:#888;">No JSON records found yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jsonRecords as $idx => $jr): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($jr["fullName"] ?? ""); ?></strong></td>
                            <td><?php echo htmlspecialchars($jr["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($jr["mobile"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($jr["course"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($jr["year"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($jr["gender"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($jr["submittedAt"] ?? ""); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 style="margin-top:40px; margin-bottom:15px;">Contact Inquiries (from CSV file)</h3>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contactRows)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:20px; color:#888;">No contact inquiries found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contactRows as $idx => $cr): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($cr[0] ?? ""); ?></strong></td>
                            <td><?php echo htmlspecialchars($cr[1] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($cr[2] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($cr[3] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($cr[4] ?? ""); ?></td>
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
