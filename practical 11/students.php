<?php
require_once "db.php";

$search = trim($_GET["search"] ?? "");
$courseFilter = trim($_GET["course"] ?? "");
$yearFilter = trim($_GET["year"] ?? "");
$statusFilter = trim($_GET["status"] ?? "");
$page = max(1, intval($_GET["page"] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($courseFilter)) {
    $whereClauses[] = "course = ?";
    $params[] = $courseFilter;
}
if (!empty($yearFilter)) {
    $whereClauses[] = "year = ?";
    $params[] = $yearFilter;
}
if (!empty($statusFilter) && in_array($statusFilter, ["active", "inactive"], true)) {
    $whereClauses[] = "status = ?";
    $params[] = $statusFilter;
}

$whereSql = "";
if (!empty($whereClauses)) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM students $whereSql");
$countStmt->execute($params);
$totalRecords = $countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRecords / $limit));

$sql = "SELECT id, name, email, mobile, course, year, gender, status FROM students $whereSql ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$msg = $_GET["msg"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>StudentHub | Student Management CRUD (Practical 11)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="students.php" class="m3">Manage Students</a>
<a href="add_student.php" class="m2">Add Student</a>
<a href="export_csv.php" class="m4">Export CSV</a>
<a href="dashboard.html" class="m5">Dashboard</a>
<a href="events.html" class="m6">Events</a>
<a href="profile.html" class="m7">Profile</a>
<a href="contact.html" class="m8">Contact</a>
<a href="faq.html" class="m9">FAQ</a>
    </nav>
</header>

<div class="page-box">
    <h2>Student Management System (CRUD)</h2>
    <p style="text-align:center; color:#555; margin-bottom:20px;">Practical 11: Create, Read, Update, Soft-Delete, Filter, and Export CSV</p>

    <?php if ($msg === "added"): ?>
        <div class="alert-success">New student record successfully added!</div>
    <?php elseif ($msg === "updated"): ?>
        <div class="alert-success">Student details updated successfully!</div>
    <?php elseif ($msg === "deactivated"): ?>
        <div class="alert-success" style="background:#fff3e0; color:#ef6c00;">Student status set to Inactive (Soft-deleted).</div>
    <?php elseif ($msg === "activated"): ?>
        <div class="alert-success">Student status restored to Active!</div>
    <?php endif; ?>

    <form method="GET" action="students.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search name or email..." value="<?php echo htmlspecialchars($search); ?>" style="flex:1; min-width:180px;">

        <select name="course">
            <option value="">All Courses</option>
            <option value="Computer Engineering" <?php if ($courseFilter === "Computer Engineering") echo "selected"; ?>>Computer Engineering</option>
            <option value="Information Technology" <?php if ($courseFilter === "Information Technology") echo "selected"; ?>>Information Technology</option>
            <option value="Civil Engineering" <?php if ($courseFilter === "Civil Engineering") echo "selected"; ?>>Civil Engineering</option>
            <option value="Mechanical Engineering" <?php if ($courseFilter === "Mechanical Engineering") echo "selected"; ?>>Mechanical Engineering</option>
        </select>

        <select name="year">
            <option value="">All Years</option>
            <option value="First Year" <?php if ($yearFilter === "First Year") echo "selected"; ?>>First Year</option>
            <option value="Second Year" <?php if ($yearFilter === "Second Year") echo "selected"; ?>>Second Year</option>
            <option value="Third Year" <?php if ($yearFilter === "Third Year") echo "selected"; ?>>Third Year</option>
            <option value="Fourth Year" <?php if ($yearFilter === "Fourth Year") echo "selected"; ?>>Fourth Year</option>
        </select>

        <select name="status">
            <option value="">All Statuses</option>
            <option value="active" <?php if ($statusFilter === "active") echo "selected"; ?>>Active Only</option>
            <option value="inactive" <?php if ($statusFilter === "inactive") echo "selected"; ?>>Inactive Only</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="students.php" class="btn btn-warning">Reset</a>
    </form>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
        <div><strong>Total Found:</strong> <?php echo $totalRecords; ?> students</div>
        <div>
            <a href="add_student.php" class="btn btn-primary" style="margin-right:10px;">+ Add New Student</a>
            <a href="export_csv.php" class="btn btn-warning">Export to CSV</a>
        </div>
    </div>

    <div class="table-container">
        <table class="record-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:20px; color:#888;">No student records match the criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo $s["id"]; ?></td>
                            <td><strong><?php echo htmlspecialchars($s["name"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($s["email"]); ?></td>
                            <td><?php echo htmlspecialchars($s["mobile"] ?? "N/A"); ?></td>
                            <td><?php echo htmlspecialchars($s["course"]); ?></td>
                            <td><?php echo htmlspecialchars($s["year"]); ?></td>
                            <td><?php echo htmlspecialchars($s["gender"]); ?></td>
                            <td>
                                <span class="badge <?php echo ($s["status"] === "active") ? "badge-success" : "badge-danger"; ?>">
                                    <?php echo htmlspecialchars(ucfirst($s["status"])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $s["id"]; ?>" class="btn-small" style="text-decoration:none; margin-right:5px;">Edit</a>
                                <?php if ($s["status"] === "active"): ?>
                                    <a href="delete_student.php?id=<?php echo $s["id"]; ?>&action=deactivate" class="btn-small btn-danger" style="text-decoration:none;" onclick="return confirm('Soft delete this student?');">Deactivate</a>
                                <?php else: ?>
                                    <a href="delete_student.php?id=<?php echo $s["id"]; ?>&action=activate" class="btn-small btn-green" style="text-decoration:none;">Activate</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div style="text-align:center; margin-top:20px;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="students.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&course=<?php echo urlencode($courseFilter); ?>&year=<?php echo urlencode($yearFilter); ?>&status=<?php echo urlencode($statusFilter); ?>"
                   class="btn <?php echo ($page === $i) ? 'btn-primary' : 'btn-warning'; ?>"
                   style="padding:6px 12px; margin:2px;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>2026 StudentHub Portal. All rights reserved.</p>
</footer>

</body>
</html>
