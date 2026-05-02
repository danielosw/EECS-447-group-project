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
        <li><a href="admin_login.php" class="active">Admin</a></li>
    </ul>
</nav>
<main>
    <div class="card" style="max-width:400px;margin:2rem auto">
        <div class="card-title">Admin Page</div>
        <p>Welcome, Admin!</p>
        <p>Add a new player to the database.</p>
        <p>Fill in the username, level, server.</p>
        <form action="add_player_process.php" method="post">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br><br>
            <label for="level">Level:</label>
            <input type="text" id="level" name="level" required>
            <br><br>
            <label for="server">Server:</label>
            <input type="text" id="server" name="server" required>
            <br><br>

            <button type="submit" class="btn">Add Player</button>
        </form>
</div>  
</main>
</body>
</html>
