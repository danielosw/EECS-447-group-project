<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}

# fetch characters for a dropdown list
$chars = $conn->query("SELECT character_id, name FROM CHARACTERS ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Character — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="admin.php" class="active">Admin</a></li>
    </ul>
</nav>
<main>
    <div class="card" style="max-width:400px;margin:2rem auto">
        <div class="card-title">Update Character</div>
        <form action="update_character_process.php" method="post">
            <label>Select Character:</label>
            <select name="character_id" required style="margin-bottom: 1rem; width: 100%; padding: 0.5rem;">
                <option value="">-- Choose Character --</option>
                <?php foreach ($chars as $c): ?>
                    <option value="<?= $c['character_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <label>New Role:</label>
            <input type="text" name="role" placeholder="e.g. Tank, Damage, Support" required style="margin-bottom: 1rem; width: 100%; padding: 0.5rem;">
            <label>New Difficulty (1-10):</label>
            <input type="number" name="difficulty" min="1" max="10" placeholder="1-10" required style="margin-bottom: 1rem; width: 100%; padding: 0.5rem;">
            <button type="submit" class="btn">Update Character</button>
        </form>
    </div>  
</main>
</body>
</html>