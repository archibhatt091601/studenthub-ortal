<?php
session_start();
$_SESSION = [];
session_destroy();
setcookie("remember_email", "", time() - 3600, "/");
header("Location: login.php?logout=1");
exit;
?>