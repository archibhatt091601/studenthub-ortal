<?php
require_once "db.php";

$id = intval($_GET["id"] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT poster_image FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $ev = $stmt->fetch();
    if ($ev && !empty($ev["poster_image"])) {
        $filePath = __DIR__ . "/uploads/" . $ev["poster_image"];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    $del = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $del->execute([$id]);
    header("Location: events.php?msg=deleted");
    exit;
}

header("Location: events.php");
exit;
?>