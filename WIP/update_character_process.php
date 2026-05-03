<?php
require 'db_connect.php';
# kick out if not logged in
if (!$_SESSION['admin_logged_in']) {
    header('Location: login_admin.php');
    exit;
}

# get form data
$character_id = (int)$_POST['character_id'];
$role = $_POST['role'];
$difficulty = (int)$_POST['difficulty'];

# execute updating query
$sql = "UPDATE CHARACTERS SET role = ?, difficulty = ? WHERE character_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sii', $role, $difficulty, $character_id);
$stmt->execute();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Character Updated — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
</nav>
<main>
    <div class="card" style="max-width:400px;margin:2rem auto;text-align:center;">
        <div class="card-title">Character Updated</div>
        <p>The character stats have been updated successfully!</p>
        <p style="margin-top: 1rem;"><a href="admin.php" class="btn">Return to Admin Panel</a></p>
    </div>
</main>
</body>
</html>