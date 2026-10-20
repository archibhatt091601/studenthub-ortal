<?php
require_once "db.php";

$id = intval($_GET["id"] ?? 0);
$action = $_GET["action"] ?? "deactivate";

if ($id > 0) {
    if ($action === "activate") {
        $stmt = $pdo->prepare("UPDATE students SET status = 'active' WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: students.php?msg=activated");
    } else {
        $stmt = $pdo->prepare("UPDATE students SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: students.php?msg=deactivated");
    }
    exit;
}

header("Location: students.php");
exit;
?>