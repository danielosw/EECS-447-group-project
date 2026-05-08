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
        <p>Remove a player from the database.</p>
        <p>Fill in the player ID.</p>
        <form action="delete_player_process.php" method="post">

            <label for="player_id">Player ID:</label>
            <input type="text" id="player_id" name="player_id" required>
            <br><br>

            <button type="submit" class="btn">Remove Player</button>
        </form>
</div>  
</main>
</body>
</html>
