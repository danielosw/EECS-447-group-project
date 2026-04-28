<?php
require 'db_connect.php';

$char_name = trim($_POST['charname'] ?? '');
if (!$char_name) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM CHARACTERS WHERE name = ?");
$stmt->bind_param("s", $char_name);
$stmt->execute();
$character = $stmt->get_result()->fetch_assoc();

$stats      = null;
$matches    = [];
$win_rate   = 0;
$play_rate  = 0;

if ($character) {
    $cid = $character['character_id'];
    $total = $conn->query("SELECT COUNT(*) FROM MATCH_STATS")->fetch_row()[0];

    $stmt = $conn->prepare("
        SELECT COUNT(*)                              AS plays,
               SUM(win_loss = 'Win')                AS wins,
               ROUND(AVG(kills),   1)               AS avg_k,
               ROUND(AVG(deaths),  1)               AS avg_d,
               ROUND(AVG(assists), 1)               AS avg_a
        FROM MATCH_STATS WHERE character_id = ?
    ");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $win_rate  = $stats['plays'] > 0 ? round($stats['wins']  / $stats['plays'] * 100, 1) : 0;
    $play_rate = $total           > 0 ? round($stats['plays'] / $total          * 100, 1) : 0;

    $stmt = $conn->prepare("
        SELECT M.match_id, M.date, M.duration, M.map, M.game_mode,
               P.username, MS.kills, MS.deaths, MS.assists, MS.win_loss
        FROM MATCH_STATS MS
        JOIN MATCHES M  ON MS.match_id  = M.match_id
        JOIN PLAYERS P  ON MS.player_id = P.player_id
        WHERE MS.character_id = ?
        ORDER BY M.date DESC LIMIT 10
    ");
    $stmt->bind_param("i", $cid);
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
    <title><?= htmlspecialchars($char_name) ?> — StatTracker</title>
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
    <a href="index.php" class="back-link">← Back</a>

    <?php if ($character === null): ?>
        <div class="empty-state">
            <div class="icon">🔍</div>
            <p>Character "<strong><?= htmlspecialchars($char_name) ?></strong>" not found.</p>
        </div>
    <?php else: ?>

    <div class="card">
        <div class="profile-header">
            <div class="avatar avatar-char"><?= strtoupper($character['name'][0]) ?></div>
            <div class="profile-info">
                <h1><?= htmlspecialchars($character['name']) ?></h1>
                <div class="meta" style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;margin-top:0.4rem">
                    <span class="badge badge-role"><?= htmlspecialchars($character['role']) ?></span>
                    <span>Difficulty</span>
                    <div class="pip-row">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <div class="pip <?= $i <= $character['difficulty'] ? 'on' : '' ?>"></div>
                        <?php endfor; ?>
                    </div>
                    <span><?= $character['difficulty'] ?>/10</span>
                </div>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-box <?= $win_rate >= 50 ? 's-win' : 's-loss' ?>">
                <div class="val"><?= $win_rate ?>%</div>
                <div class="lbl">Win Rate</div>
            </div>
            <div class="stat-box s-gold">
                <div class="val"><?= $play_rate ?>%</div>
                <div class="lbl">Play Rate</div>
            </div>
            <div class="stat-box">
                <div class="val"><?= $stats['plays'] ?></div>
                <div class="lbl">Total Picks</div>
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

    <div class="card">
        <div class="card-title">Recent Appearances</div>
        <?php if (!$matches): ?>
            <div class="empty-state" style="padding:2rem">No matches recorded.</div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Result</th><th>Player</th><th>K/D/A</th><th>Mode</th><th>Map</th><th>Date</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $m): ?>
                    <tr>
                        <td><span class="badge badge-<?= strtolower($m['win_loss']) ?>"><?= $m['win_loss'] ?></span></td>
                        <td>
                            <form action="player_profile.php" method="post" style="display:inline">
                                <input type="hidden" name="username" value="<?= htmlspecialchars($m['username']) ?>">
                                <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($m['username']) ?></button>
                            </form>
                        </td>
                        <td>
                            <span class="kda-k"><?= $m['kills'] ?></span> /
                            <span class="kda-d"><?= $m['deaths'] ?></span> /
                            <span class="kda-a"><?= $m['assists'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($m['game_mode']) ?></td>
                        <td><?= htmlspecialchars($m['map']) ?></td>
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
