<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentRole = $_SESSION["user_role"] ?? "guest";
$currentName = $_SESSION["user_name"] ?? "";
?>
<header>
    <h1><span class="title-badge">StudentHub Portal</span></h1>
    <nav class="ttt">
<a href="home.html" class="m">Home</a>
<a href="about.html" class="m1">About</a>
<a href="admin_dashboard.php" class="m3">Admin Dashboard</a>
<a href="login.php" class="m9">Login</a>
<a href="logout.php" class="m2">Logout</a>
<a href="dashboard.html" class="m4">Dashboard</a>
<a href="events.html" class="m5">Events</a>
<a href="profile.html" class="m6">Profile</a>
<a href="contact.html" class="m7">Contact</a>
<a href="faq.html" class="m8">FAQ</a>
    </nav>
</header>
