<?php
require_once "db.php";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=students_export_" . date("Ymd_His") . ".csv");

$out = fopen("php://output", "w");
fputcsv($out, ["ID", "Name", "Email", "Mobile", "Course", "Year", "Gender", "Status", "Created At"]);

$stmt = $pdo->query("SELECT id, name, email, mobile, course, year, gender, status, created_at FROM students ORDER BY id ASC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, $row);
}
fclose($out);
exit;
?>