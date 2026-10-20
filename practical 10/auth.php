<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$sessionTimeout = 900;

if (isset($_SESSION["last_activity"])) {
    $elapsed = time() - $_SESSION["last_activity"];
    if ($elapsed > $sessionTimeout) {
        $_SESSION = [];
        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }
}
$_SESSION["last_activity"] = time();

function require_login() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php?auth_required=1");
        exit;
    }
}

function require_admin() {
    require_login();
    if (($_SESSION["user_role"] ?? "") !== "admin") {
        header("Location: student_dashboard.php?access_denied=1");
        exit;
    }
}
?>