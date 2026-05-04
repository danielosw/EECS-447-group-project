<?php
require 'db_connect.php';

$username = trim($_GET['username'] ?? '');
if (!$username) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM PLAYERS WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$player = $stmt->get_result()->fetch_assoc();

$stats      = null;
$top_chars  = [];
$matches    = [];
$win_rate   = 0;

if ($player) {
    $pid = $player['player_id'];

    $stmt = $conn->prepare("
        SELECT COUNT(*)                              AS total,
               SUM(win_loss = 'Win')                AS wins,
               SUM(win_loss = 'Loss')               AS losses,
               ROUND(AVG(kills),   1)               AS avg_k,
               ROUND(AVG(deaths),  1)               AS avg_d,
               ROUND(AVG(assists), 1)               AS avg_a
        FROM MATCH_STATS WHERE player_id = ?
    ");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $win_rate = $stats['total'] > 0 ? round($stats['wins'] / $stats['total'] * 100, 1) : 0;

    $stmt = $conn->prepare("
        SELECT C.name, C.role, COUNT(*) AS plays,
               ROUND(SUM(MS.win_loss = 'Win') / COUNT(*) * 100, 1) AS wr
        FROM MATCH_STATS MS
        JOIN CHARACTERS C ON MS.character_id = C.character_id
        WHERE MS.player_id = ?
        GROUP BY C.character_id ORDER BY plays DESC LIMIT 3
    ");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $top_chars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("
        SELECT M.match_id, M.date, M.duration, M.map, M.game_mode,
               C.name AS char_name, C.role,
               MS.kills, MS.deaths, MS.assists, MS.win_loss
        FROM MATCH_STATS MS
        JOIN MATCHES M    ON MS.match_id    = M.match_id
        JOIN CHARACTERS C ON MS.character_id = C.character_id
        WHERE MS.player_id = ?
        ORDER BY M.date DESC LIMIT 10
    ");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($username) ?> — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="login_admin.php">Admin</a></li>
    </ul>
</nav>
<main>
    <a href="javascript:history.back()" class="back-link">← Back</a>

    <?php if ($player === null): ?>
        <div class="empty-state">
            <div class="icon">🔍</div>
            <p>Player "<strong><?= htmlspecialchars($username) ?></strong>" not found.</p>
        </div>
    <?php else: ?>

    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?= strtoupper($username[0]) ?></div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($player['username']) ?></h1>
                <div class="meta">
                    Level <?= $player['level'] ?> &nbsp;·&nbsp;
                    <?= htmlspecialchars($player['server']) ?> Server &nbsp;·&nbsp;
                    Joined <?= date('M j, Y', strtotime($player['creation_date'])) ?> &nbsp;·&nbsp;
                    ID <?= $player['player_id'] ?>
                </div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-box <?= $win_rate >= 50 ? 's-win' : 's-loss' ?>">
                <div class="val"><?= $win_rate ?>%</div>
                <div class="lbl">Win Rate</div>
            </div>
            <div class="stat-box">
                <div class="val"><?= $stats['total'] ?></div>
                <div class="lbl">Matches</div>
            </div>
            <div class="stat-box s-win">
                <div class="val"><?= $stats['wins'] ?></div>
                <div class="lbl">Wins</div>
            </div>
            <div class="stat-box s-loss">
                <div class="val"><?= $stats['losses'] ?></div>
                <div class="lbl">Losses</div>
            </div>
            <div class="stat-box s-gold">
                <div class="val"><?= $stats['avg_k'] ?? '—' ?></div>
                <div class="lbl">Avg Kills</div>
            </div>
            <div class="stat-box">
                <div class="val"><?= $stats['avg_d'] ?? '—' ?></div>
                <div class="lbl">Avg Deaths</div>
            </div>
            <div class="stat-box s-blue">
                <div class="val"><?= $stats['avg_a'] ?? '—' ?></div>
                <div class="lbl">Avg Assists</div>
            </div>
        </div>
    </div>

    <?php if ($top_chars): ?>
    <div class="card">
        <div class="card-title">Most Played Characters</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Character</th><th>Role</th><th>Games</th><th>Win Rate</th></tr></thead>
                <tbody>
                    <?php foreach ($top_chars as $c): ?>
                    <tr>
                        <td>
                            <form action="char_profile.php" method="get" style="display:inline">
                                <input type="hidden" name="charname" value="<?= htmlspecialchars($c['name']) ?>">
                                <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($c['name']) ?></button>
                            </form>
                        </td>
                        <td><span class="badge badge-role"><?= htmlspecialchars($c['role']) ?></span></td>
                        <td><?= $c['plays'] ?></td>
                        <td style="color:<?= $c['wr'] >= 50 ? 'var(--win)' : 'var(--loss)' ?>;font-weight:700"><?= $c['wr'] ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-title">Recent Matches</div>
        <?php if (!$matches): ?>
            <div class="empty-state" style="padding:2rem">No matches found.</div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Result</th><th>Character</th><th>K/D/A</th><th>Mode</th><th>Map</th><th>Time</th><th>Date</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $m): ?>
                    <tr>
                        <td><span class="badge badge-<?= strtolower($m['win_loss']) ?>"><?= $m['win_loss'] ?></span></td>
                        <td>
                            <form action="char_profile.php" method="get" style="display:inline">
                                <input type="hidden" name="charname" value="<?= htmlspecialchars($m['char_name']) ?>">
                                <button type="submit" class="link-btn link-btn-plain"><?= htmlspecialchars($m['char_name']) ?></button>
                            </form>
                        </td>
                        <td>
                            <span class="kda-k"><?= $m['kills'] ?></span> /
                            <span class="kda-d"><?= $m['deaths'] ?></span> /
                            <span class="kda-a"><?= $m['assists'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($m['game_mode']) ?></td>
                        <td><?= htmlspecialchars($m['map']) ?></td>
                        <td style="color:var(--muted)"><?= $m['duration'] ?>m</td>
                        <td style="color:var(--muted)"><?= date('M j', strtotime($m['date'])) ?></td>
                        <td><a href="match_profile.php?match_id=<?= $m['match_id'] ?>" class="btn btn-sm btn-ghost">Details</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>
</main>
</body>
</html>
