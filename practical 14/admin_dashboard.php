<?php
require_once "db.php";

session_start();
if (!isset($_SESSION["user_id"])) {
    $_SESSION["user_id"] = 1;
    $_SESSION["user_name"] = "Admin User";
    $_SESSION["user_role"] = "admin";
    $_SESSION["user_email"] = "admin@studenthub.com";
}

$adminEmail = $_SESSION["user_email"] ?? "admin@studenthub.com";
$msg = "";

$action = $_GET["action"] ?? "";
$targetId = intval($_GET["id"] ?? 0);

if ($action === "toggle_role" && $targetId > 0) {
    $uStmt = $pdo->prepare("SELECT role, email FROM users WHERE id = ?");
    $uStmt->execute([$targetId]);
    $targetUser = $uStmt->fetch();
    if ($targetUser) {
        $newRole = ($targetUser["role"] === "admin") ? "student" : "admin";
        $upd = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $upd->execute([$newRole, $targetId]);

        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->execute([$adminEmail, "ROLE_CHANGE", "Changed role of " . $targetUser["email"] . " to " . $newRole, $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);
        $msg = "Role for user #" . $targetId . " changed to " . ucfirst($newRole) . ".";
    }
}

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST" && isset($_POST["log_audit"])) {
    $customAction = trim($_POST["custom_action"] ?? "");
    $customDetails = trim($_POST["custom_details"] ?? "");
    if (!empty($customAction)) {
        $logStmt = $pdo->prepare("INSERT INTO audit_logs (user_email, action, details, ip_address) VALUES (?, ?, ?, ?)");
        $logStmt->execute([$adminEmail, $customAction, $customDetails, $_SERVER["REMOTE_ADDR"] ?? "127.0.0.1"]);
        $msg = "Administrative audit event successfully recorded.";
    }
}

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalRegistrations = $pdo->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

$usersList = $pdo->query("SELECT id, name, email, role, created_at, last_login FROM users ORDER BY id DESC LIMIT 10")->fetchAll();

$recentRegistrations = $pdo->query("SELECT r.id, s.name AS student_name, e.title AS event_title, r.registration_date, r.status
                                    FROM registrations r
                                    JOIN students s ON r.student_id = s.id
                                    JOIN events e ON r.event_id = e.id
                                    ORDER BY r.registration_date DESC LIMIT 5")->fetchAll();

$auditLogs = $pdo->query("SELECT id, user_email, action, details, ip_address, created_at FROM audit_logs ORDER BY id DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Executive Admin Dashboard (Practical 14)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "menu.php"; ?>

<div class="page-box">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2>Executive Administrative Dashboard</h2>
        <div>
            <span class="badge badge-danger" style="font-size:14px; padding:6px 12px; margin-right:10px;">Role: Admin (<?php echo htmlspecialchars($_SESSION["user_name"] ?? ""); ?>)</span>
            <a href="logout.php" class="btn btn-warning">Log Out</a>
        </div>
    </div>
    <p style="color:#555; margin-bottom:20px;">Practical 14: Centralized administration summarizing metrics, user management, activity feeds, and system audit logs</p>

    <?php if (!empty($msg)): ?>
        <div class="alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="stat-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>
        <div class="stat-card">
            <h3>Student Profiles</h3>
            <p><?php echo $totalStudents; ?></p>
        </div>
        <div class="stat-card">
            <h3>Campus Events</h3>
            <p><?php echo $totalEvents; ?></p>
        </div>
        <div class="stat-card">
            <h3>Registrations</h3>
            <p><?php echo $totalRegistrations; ?></p>
        </div>
    </div>

    <div style="margin-top:35px; display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <h3>User Management Interface</h3>
    </div>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Registered At</th>
                    <th>Action</th>
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
                        <td>
                            <a href="admin_dashboard.php?action=toggle_role&id=<?php echo $u["id"]; ?>"
                               class="btn-small btn-warning"
                               style="text-decoration:none;"
                               onclick="return confirm('Toggle role between Admin and Student?');">
                                Toggle Role
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h3 style="margin-top:35px; margin-bottom:15px;">Recent Event Registrations</h3>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Event Title</th>
                    <th>Registration Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentRegistrations)): ?>
                    <tr><td colspan="5" style="text-align:center; padding:15px; color:#888;">No recent registrations found.</td></tr>
                <?php else: ?>
                    <?php foreach ($recentRegistrations as $rr): ?>
                        <tr>
                            <td><?php echo $rr["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($rr["student_name"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($rr["event_title"]); ?></td>
                            <td><?php echo htmlspecialchars($rr["registration_date"]); ?></td>
                            <td><span class="badge badge-success"><?php echo htmlspecialchars(ucfirst($rr["status"])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h3 style="margin-top:35px; margin-bottom:15px;">System Activity & Audit Logs</h3>
    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>User Email</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($auditLogs)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:15px; color:#888;">No audit logs recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($auditLogs as $al): ?>
                        <tr>
                            <td><?php echo $al["id"]; ?></td>
                            <td><?php echo htmlspecialchars($al["user_email"]); ?></td>
                            <td><span class="badge badge-warning"><?php echo htmlspecialchars($al["action"]); ?></span></td>
                            <td><?php echo htmlspecialchars($al["details"] ?? "N/A"); ?></td>
                            <td><code><?php echo htmlspecialchars($al["ip_address"] ?? "127.0.0.1"); ?></code></td>
                            <td><?php echo htmlspecialchars($al["created_at"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="background:#ffffff; border:1px solid #dbe3ee; border-radius:6px; padding:20px; margin-top:30px;">
        <h4 style="margin-bottom:10px; color:#1f4e8c;">Record Manual Audit Event</h4>
        <form method="POST" action="admin_dashboard.php" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
            <input type="hidden" name="log_audit" value="1">
            <input type="text" name="custom_action" placeholder="Action Code (e.g. SYSTEM_CHECK)" required style="padding:8px 12px; border:1px solid #ccc; border-radius:4px; flex:1; min-width:200px;">
            <input type="text" name="custom_details" placeholder="Details (e.g. Verified database integrity)" required style="padding:8px 12px; border:1px solid #ccc; border-radius:4px; flex:2; min-width:260px;">
            <button type="submit" class="btn btn-primary" style="padding:8px 18px;">Log Event</button>
        </form>
    </div>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
