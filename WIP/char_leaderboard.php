<?php
require 'db_connect.php';

$rank = 1;
$sort = $_GET['sort'] ?? 'winrate';
$order = match($sort) {
    'winrate' => 'winrate DESC',
    'plays' => 'total DESC',
    default => 'winrate DESC'
};

$stmt = $conn->prepare(
    "SELECT C.name, role,
            COUNT(*) AS total,
            ROUND(SUM(MS.win_loss = 'Win')/COUNT(*) * 100, 1) AS winrate
    FROM MATCH_STATS MS
    JOIN CHARACTERS C ON MS.character_id = C.character_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    GROUP BY C.character_id ORDER BY $order
");
$stmt->execute();
$characters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Characters</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php" class="active">Leaderboard</a></li>
        <li><a href="login_admin.php">Admin</a></li>
    </ul>
</nav>

<main>
    <div class="tabs">
        <a href="leaderboard.php" class="tab">Players</a>
        <a href="char_leaderboard.php" class="tab active">Characters</a>
    </div>

    <form method="get" style="margin-bottom:1rem">
        <select name="sort" onchange="this.form.submit()">
            <option value="winrate" <?= $sort === 'winrate' ? 'selected' : '' ?>>Win Rate</option>
            <option value="plays" <?= $sort === 'plays' ? 'selected' : '' ?>>Plays</option>
        </select>
    </form>

    <div class="card">
        <div class="card-title">Characters</div>
        <?php if (!$characters): ?>
            <div class="empty-state" style="padding:2rem">No characters found.</div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th><th>Name</th><th>Role</th><th>Plays</th><th>Win Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($characters as $c): ?>
                        <tr>
                            <td><?= $rank ?><?php $rank++; ?></td>
                            <td>
                                <form action="char_profile.php" method="post" style="display:inline">
                                    <input type="hidden" name="charname" value="<?= htmlspecialchars($c['name']) ?>">
                                    <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($c['name']) ?></button>
                                </form>
                            </td>
                            <td><?= $c['role'] ?></td>
                            <td><?= $c['total'] ?></td>
                            <td style="color:<?= $c['winrate'] >= 50 ? 'var(--win)' : 'var(--loss)' ?>"><?= $c['winrate'] ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>
</body>
</html>