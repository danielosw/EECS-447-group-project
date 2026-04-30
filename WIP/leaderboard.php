<?php
require 'db_connect.php';

$rank = 1;
$sort = $_GET['sort'] ?? 'winrate';
$region = $_GET['region'] ?? 'all';
$order = match($sort) {
    'level' => 'P.level DESC',
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

$stmt = $conn->prepare(
    "SELECT username, level, server,
            SUM(win_loss = 'Win') AS wins,
            SUM(win_loss = 'Loss') AS losses,
            ROUND(SUM(MS.win_loss = 'Win')/COUNT(*) * 100, 1) AS winrate
    FROM MATCH_STATS MS
    JOIN PLAYERS P ON MS.player_id = P.player_id
    JOIN MATCHES M ON MS.match_id = M.match_id
    WHERE game_mode = 'Ranked'
    $region_filter
    GROUP BY P.player_id ORDER BY $order
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
    <title><?= htmlspecialchars("Leaderboard") ?> - Ranked</title>
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
        <a href="leaderboard.php" class="tab active">Players</a>
        <a href="char_leaderboard.php" class="tab">Characters</a>
    </div>

    <form method="get" style="margin-bottom:1rem">
        <select name="region" onchange="this.form.submit()">
            <option value="all" <?= $region === 'all' ? 'selected' : '' ?>>All</option>
            <option value="NA" <?= $region === 'NA' ? 'selected' : '' ?>>NA</option>
            <option value="KR" <?= $region === 'KR' ? 'selected' : '' ?>>KR</option>
            <option value="EU" <?= $region === 'EU' ? 'selected' : '' ?>>EU</option>
            <option value="CN" <?= $region === 'CN' ? 'selected' : '' ?>>CN</option>
        </select>

        <select name="sort" onchange="this.form.submit()">
            <option value="winrate" <?= $sort === 'winrate' ? 'selected' : '' ?>>Win Rate</option>
            <option value="level" <?= $sort === 'level' ? 'selected' : '' ?>>Level</option>
        </select>
    </form>

    <div class="card">
        <div class="card-title">Ranked Players</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th><th>Player</th><th>Region</th><th>Level</th><th>Win Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$players): ?>
                        <tr><td colspan="5" style="text-align:center">No players found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($players as $p): ?>
                        <tr>
                            <td><?= $rank ?><?php $rank++; ?></td>
                            <td>
                                <form action="player_profile.php" method="post" style="display:inline">
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
    </div>

</main>
</body>
</html>