<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}

# get form data
$username = $_POST['username'];
$level = (int)$_POST['level'];
$server = $_POST['server'];

# generate player id and creation time
$player_id = rand(10000, 99999);
$creation_date = date('Y-m-d H:i:s');

# insert into database
$sql = "INSERT INTO PLAYERS (PLAYER_ID, USERNAME, LEVEL, SERVER, creation_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('isiss', $player_id, $username, $level, $server, $creation_date);
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
        <div class="card-title">Player Added</div>
        <p>Player <strong><?php echo htmlspecialchars($username); ?></strong> has been added successfully!</p>
        <p>Player ID: <strong><?php echo $player_id; ?></strong></p>
        <p><a href="add_player.php">Add another player</a></p>
    </div>
</main>
</body>
</html>
