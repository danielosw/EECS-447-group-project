<?php
require 'db_connect.php';
# redirect if already logged in
if ($_SESSION['admin_logged_in']) {
    header('Location: admin.php');
    exit;
}
# redirect if password is correct
if (isset($_POST['password']) && $_POST['password'] === 'admin123')
{
    $_SESSION['admin_logged_in'] = true;
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="admin.php">Admin</a></li>
    </ul>
</nav>
<main>
    <a href="index.php" class="back-link">← Back</a>

    <div class="card" style="max-width:400px;margin:2rem auto">
        <div class="card-title">Admin Login</div>
        <form action="login_admin.php" method="post" class="admin-login-form">
            <input type="password" name="password" placeholder="Enter admin password…" required>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</main>
</body>
</html>
