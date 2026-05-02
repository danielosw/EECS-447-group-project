<?php
require 'db_connect.php';
$players    = $conn->query("SELECT player_id, username, level, server FROM PLAYERS ORDER BY level DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$characters = $conn->query("SELECT character_id, name, role, difficulty FROM CHARACTERS LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StatTracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" class="nav-brand">⚔ StatTracker</a>
    <ul class="nav-links">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="login_admin.php">Admin</a></li>
    </ul>
</nav>

<main>
    <div class="hero">
        <h1>Game Stat Tracker</h1>
        <p>Look up player profiles, browse characters, and explore match history.</p>

        <div class="search-grid">
            <div class="search-card">
                <h2>Search Player</h2>
                <form class="search-form" action="player_profile.php" method="get">
                    <input type="text" name="username" placeholder="Enter username…" list="player_list" autocomplete="off" required>
                    <datalist id="player_list">
                        <?php foreach ($players as $p): ?>
                            <option value="<?= htmlspecialchars($p['username']) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>

            <div class="search-card">
                <h2>Search Character</h2>
                <form class="search-form" action="char_profile.php" method="get">
                    <input type="text" name="charname" placeholder="Enter character…" list="char_list" autocomplete="off" required>
                    <datalist id="char_list">
                        <?php foreach ($characters as $c): ?>
                            <option value="<?= htmlspecialchars($c['name']) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>
        </div>
    </div>

    <div class="list-grid">
        <div class="card">
            <div class="card-title">Players</div>
            <?php foreach ($players as $p): ?>
            <div class="list-row">
                <div>
                    <form id="pf<?= $p['player_id'] ?>" action="player_profile.php" method="get" style="display:inline">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($p['username']) ?>">
                        <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($p['username']) ?></button>
                    </form>
                    <span style="color:var(--muted);font-size:0.8rem;margin-left:0.5rem"><?= htmlspecialchars($p['server']) ?></span>
                </div>
                <span style="color:var(--muted);font-size:0.82rem">Lv.&nbsp;<?= $p['level'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="card">
            <div class="card-title">Characters</div>
            <?php foreach ($characters as $c): ?>
            <div class="list-row">
                <div>
                    <form action="char_profile.php" method="get" style="display:inline">
                        <input type="hidden" name="charname" value="<?= htmlspecialchars($c['name']) ?>">
                        <button type="submit" class="link-btn link-btn-accent"><?= htmlspecialchars($c['name']) ?></button>
                    </form>
                    <span class="badge badge-role" style="margin-left:0.5rem"><?= htmlspecialchars($c['role']) ?></span>
                </div>
                <div class="pip-row">
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="pip <?= $i <= $c['difficulty'] ? 'on' : '' ?>"></div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
</body>
</html>
