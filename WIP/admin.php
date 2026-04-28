<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="admin_login.php">Admin</a></li>
    </ul>
</nav>
<main>
    <a href="index.php" class="back-link">← Back</a>
    <div class="card" style="max-width:400px;margin:2rem auto">
        <div class="card-title">Admin Page</div>
        <p>Placeholder</p>
        <form action="logout_admin.php" method="post">
            <button type="submit" class="btn">Logout</button>
        </form>
    </div>
</main>
</body>
</html>
