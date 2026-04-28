<?php
require 'db_connect.php';

$match_id = isset($_GET['match_id']) ? (int)$_GET['match_id'] : 0;
$match = null;
$stats = [];

if ($match_id) {
    $stmt = $conn->prepare("SELECT * FROM MATCHES WHERE match_id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $match = $stmt->get_result()->fetch_assoc();

    if ($match) {
        $stmt = $conn->prepare("
            SELECT P.username, C.name AS char_name, C.role,
                   MS.kills, MS.deaths, MS.assists, MS.win_loss
            FROM MATCH_STATS MS
            JOIN PLAYERS P    ON MS.player_id    = P.player_id
            JOIN CHARACTERS C ON MS.character_id = C.character_id
            WHERE MS.match_id = ?
            ORDER BY MS.win_loss ASC, MS.kills DESC
        ");
        $stmt->bind_param("i", $match_id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match #<?= $match_id ?> — StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="admin.php">Admin</a></li>
    </ul>
</nav>
<main>
    <a href="javascript:history.back()" class="back-link">← Back</a>

    <?php if (!$match): ?>
        <div class="empty-state">
            <div class="icon">🔍</div>
            <p>Match not found.</p>
        </div>
    <?php else: ?>

    <div class="card">
        <div class="section-hdr" style="margin-bottom:1.1rem">
            <div class="section-title">Match #<?= $match['match_id'] ?></div>
            <span class="badge badge-role"><?= htmlspecialchars($match['game_mode']) ?></span>
        </div>
        <div class="stat-grid">
            <div class="stat-box">
                <div class="val" style="font-size:1rem"><?= htmlspecialchars($match['map']) ?></div>
                <div class="lbl">Map</div>
            </div>
            <div class="stat-box">
                <div class="val"><?= $match['duration'] ?>m</div>
                <div class="lbl">Duration</div>
            </div>
            <div class="stat-box">
                <div class="val" style="font-size:0.95rem"><?= date('M j, Y', strtotime($match['date'])) ?></div>
                <div class="lbl">Date</div>
            </div>
            <div class="stat-box">
                <div class="val"><?= count($stats) ?></div>
                <div class="lbl">Players</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Player Stats</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Result</th><th>Player</th><th>Character</th><th>Role</th>
                        <th>Kills</th><th>Deaths</th><th>Assists</th><th>KDA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats as $s):
                        $kda = $s['deaths'] > 0
                            ? round(($s['kills'] + $s['assists']) / $s['deaths'], 2)
                            : $s['kills'] + $s['assists'];
                    ?>
                    <tr>
                        <td><span class="badge badge-<?= strtolower($s['win_loss']) ?>"><?= $s['win_loss'] ?></span></td>
                        <td>
                            <form action="player_profile.php" method="post" style="display:inline">
                                <input type="hidden" name="username" value="<?= htmlspecialchars($s['username']) ?>">
                                <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($s['username']) ?></button>
                            </form>
                        </td>
                        <td>
                            <form action="char_profile.php" method="post" style="display:inline">
                                <input type="hidden" name="charname" value="<?= htmlspecialchars($s['char_name']) ?>">
                                <button type="submit" class="link-btn link-btn-plain"><?= htmlspecialchars($s['char_name']) ?></button>
                            </form>
                        </td>
                        <td><span class="badge badge-role"><?= htmlspecialchars($s['role']) ?></span></td>
                        <td class="kda-k"><?= $s['kills'] ?></td>
                        <td class="kda-d"><?= $s['deaths'] ?></td>
                        <td class="kda-a"><?= $s['assists'] ?></td>
                        <td style="font-weight:700"><?= $kda ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>
</main>
</body>
</html>
