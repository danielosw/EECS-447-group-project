<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}

# get form data
$player_id = $_POST['player_id'];

# delete from database
$sql = "DELETE FROM PLAYERS WHERE PLAYER_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $player_id);
$stmt->execute();

$stmt->close();
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
        <div class="card-title">Player Removed</div>
        <p>Player <strong><?php echo htmlspecialchars($player_id); ?></strong> has been removed successfully!</p>
        <p>Player ID: <strong><?php echo $player_id; ?></strong></p>
        <p><a href="remove_player.php">Remove another player</a></p>
    </div>
</main>
</body>
</html>
