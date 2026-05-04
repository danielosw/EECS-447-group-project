<?php
require 'db_connect.php';

// sort and filter variables
$sort = $_GET['sort'] ?? 'winrate';
$region = $_GET['region'] ?? 'all';
$order = match($sort) {
    'level' => 'level DESC',
    'winrate' => 'winrate DESC',
    default => 'winrate DESC'
};
$region_filter = "";
$params = [];
$types = "";
if ($region !== 'all') {
    $region_filter .= " AND server = ?";
    $params[] = $region;
    $types .= "s";
}

// page variables for buttons, rank counter
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset = (($page - 1) * $per_page);
$rank = $offset + 1;

$count_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
    FROM (
    SELECT P.player_id
    FROM MATCH_STATS MS
    JOIN PLAYERS P ON MS.player_id = P.player_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    $region_filter
    GROUP BY P.player_id HAVING COUNT(*) > 3) AS sq
");
if ($region !== 'all') {
    $count_stmt->bind_param("s", $region);
}
$count_stmt->execute();
$total_players = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_players / $per_page);
$has_next = $page < $total_pages;

// pagination params
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare(
    "SELECT username, level, server,
            count(*) AS total,
            SUM(win_loss = 'Win') AS wins,
            SUM(win_loss = 'Loss') AS losses,
            ROUND(SUM(MS.win_loss = 'Win')/COUNT(*) * 100, 1) AS winrate
    FROM MATCH_STATS MS
    JOIN PLAYERS P ON MS.player_id = P.player_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    $region_filter
    GROUP BY P.player_id HAVING COUNT(*) > 3 ORDER BY $order
    LIMIT ? OFFSET ?
");
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$players = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Ranked Players</title>
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
    <h1>Ranked Players</h1>
    <div class="tabs">
        <a href="leaderboard.php" class="tab active">Players</a>
        <a href="char_leaderboard.php" class="tab">Characters</a>
    </div>

    <form method="get" class="form-grid">
        <div class="form-group">
            <select name="region" onchange="this.form.submit()">
                <option value="all" <?= $region === 'all' ? 'selected' : '' ?>>All</option>
                <option value="NA" <?= $region === 'NA' ? 'selected' : '' ?>>NA</option>
                <option value="KR" <?= $region === 'KR' ? 'selected' : '' ?>>KR</option>
                <option value="EU" <?= $region === 'EU' ? 'selected' : '' ?>>EU</option>
                <option value="CN" <?= $region === 'CN' ? 'selected' : '' ?>>CN</option>
            </select>
        </div>

        <div class="form-group">
            <select name="sort" onchange="this.form.submit()">
                <option value="winrate" <?= $sort === 'winrate' ? 'selected' : '' ?>>Win Rate</option>
                <option value="level" <?= $sort === 'level' ? 'selected' : '' ?>>Level</option>
            </select>
        </div>
    </form>

    <div class="card">
        <div class="card-title">Ranked Matches</div>
        <?php if (!$players): ?>
            <div class="empty-state" style="padding:2rem">No players found.</div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th><th>Player</th><th>Region</th><th>Level</th><th>Win Rate</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($players as $p): ?>
                        <tr>
                            <td><?= $rank ?><?php $rank++; ?></td>
                            <td>
                                <form action="player_profile.php" method="get" style="display:inline">
                                    <input type="hidden" name="username" value="<?= htmlspecialchars($p['username']) ?>">
                                    <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($p['username']) ?></button>
                                </form>
                            </td>
                            <td><?= $p['server'] ?></td>
                            <td><?= $p['level'] ?></td>
                            <td>
                                <?= $p['winrate'] ?>%&nbsp;|&nbsp;
                                <span style="color:var(--win);font-weight:700"><?= $p['wins'] ?>W</span>-
                                <span style="color:var(--loss);font-weight:700"><?= $p['losses'] ?>L</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <div>
                <?php if ($page > 1): ?>
                    <a class="btn btn-ghost" href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&region=<?= $region ?>">← Prev</a>
                <?php endif; ?>
            </div>

            <div class="pagination-center">
                Page <?= $page ?>
            </div>

            <div>
                <?php if ($has_next): ?>
                    <a class="btn" href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&region=<?= $region ?>">Next →</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php endif; ?>
    </div>

</main>
</body>
</html>