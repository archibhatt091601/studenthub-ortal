<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCurrentUser() {
    return [
        "id" => $_SESSION["user_id"] ?? null,
        "name" => $_SESSION["name"] ?? "Guest",
        "email" => $_SESSION["email"] ?? null,
        "role" => $_SESSION["role"] ?? "guest"
    ];
}

function requireAdmin() {
    $user = getCurrentUser();
    if ($user["role"] !== "admin") {
        header("Location: login.php?msg=unauthorized");
        exit;
    }
}
?>
