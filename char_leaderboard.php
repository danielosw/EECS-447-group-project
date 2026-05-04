<?php
require 'db_connect.php';

$sort = $_GET['sort'] ?? 'winrate';
$order = match($sort) {
    'winrate' => 'winrate DESC',
    'plays' => 'total DESC',
    default => 'winrate DESC'
};

$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = (($page - 1) * $per_page);
$rank = $offset + 1;

$count_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
    FROM (
    SELECT C.character_id
    FROM MATCH_STATS MS
    JOIN CHARACTERS C ON MS.character_id = C.character_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    GROUP BY C.character_id) AS sq
");
$count_stmt->execute();
$total_characters = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_characters / $per_page);
$has_next = $page < $total_pages;

$types = "ii";
$params = [$per_page, $offset];

$stmt = $conn->prepare(
    "SELECT C.name, role,
            COUNT(*) AS total,
            ROUND(SUM(MS.win_loss = 'Win')/COUNT(*) * 100, 1) AS winrate
    FROM MATCH_STATS MS
    JOIN CHARACTERS C ON MS.character_id = C.character_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    GROUP BY C.character_id ORDER BY $order
    LIMIT ? OFFSET ?
");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$characters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Ranked Characters</title>
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
    <h1>Ranked Characters</h1>
    <div class="tabs">
        <a href="leaderboard.php" class="tab">Players</a>
        <a href="char_leaderboard.php" class="tab active">Characters</a>
    </div>

    <form method="get" class="form-grid">
        <div class="form-group">
            <select name="sort" onchange="this.form.submit()">
                <option value="winrate" <?= $sort === 'winrate' ? 'selected' : '' ?>>Win Rate</option>
                <option value="plays" <?= $sort === 'plays' ? 'selected' : '' ?>>Plays</option>
            </select>
        </div>
    </form>

    <div class="card">
        <div class="card-title">Ranked Matches</div>
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
                                <form action="char_profile.php" method="get" style="display:inline">
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

        <div class="pagination">
            <div>
                <?php if ($page > 1): ?>
                    <a class="btn btn-ghost" href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>">← Prev</a>
                <?php endif; ?>
            </div>

            <div class="pagination-center">
                Page <?= $page ?>
            </div>

            <div>
                <?php if ($has_next): ?>
                    <a class="btn" href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>">Next →</a>
                <?php endif; ?>
            </div>
        </div>

        <?php endif; ?>
    </div>
</main>
</body>
</html>