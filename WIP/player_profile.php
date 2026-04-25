<?php

    // Connect to MySQL server
    $conn = new mysqli('localhost:3307', 'test', 'test', 'testdb');
    if ($conn->connect_error) {
        die('Could not connect: ' . $conn->connect_error);
    }

    // Send query for player
    $username = $_POST['username'];
    // $query = "SELECT * FROM PLAYERS WHERE USERNAME = '$username'";
    // $result = $conn->query($query);

    // prepared statement version
    $stmt = $conn->prepare("SELECT * FROM PLAYERS WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $player = $result->fetch_assoc();

    // Send query for player's match history
    $stmt = $conn->prepare(
        "SELECT M.DATE, M.DURATION, M.MAP, M.GAME_MODE FROM MATCH_STATS MS
        JOIN MATCHES M ON MS.MATCH_ID = M.MATCH_ID
        JOIN PLAYERS P ON MS.PLAYER_ID = P.PLAYER_ID
        WHERE P.USERNAME = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $matches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $player['USERNAME']; ?> - Player Profile</title>
    </head>

    <body>
        <?php if ($result->num_rows == 0): ?>
            <p>No results found.</p>
            
        <?php else: ?>
            <h1>
                <?php echo $player['USERNAME']; ?>
            </h1>
            <p>
                Level: <?php echo $player['LEVEL']; ?> <br>
                Server: <?php echo $player['SERVER']; ?> <br>
                Account Created: <?php echo $player['CREATE_TIME'] ?> <br>
                Win loss: <?php 
                 $query = "SELECT (SELECT count(*) FROM  MATCH_STATS WHERE PLAYER_ID= (SELECT PLAYER_ID FROM PLAYERS WHERE USERNAME = ?) AND WIN_LOSS = 'Win')/(SELECT count(*) FROM MATCH_STATS  WHERE PLAYER_ID = (SELECT PLAYER_ID FROM PLAYERS WHERE USERNAME = ?) ) AS 'win loss';";
                 $stmt = $conn->prepare($query);
                 $stmt->bind_param("ss", $username, $username);
                 $stmt->execute();
                 $win_loss_result = $stmt->get_result()->fetch_assoc();
                    echo $win_loss_result['win loss'];
                ?>
            </p>

            <h2>Match History</h2>
            <?php foreach ($matches as $match): ?>
                <p>
                    <?php echo $match['GAME_MODE']; ?>:
                    <?php echo $match['DATE']; ?> | 
                    <?php echo $match['DURATION']; ?> | 
                    <?php echo $match['MAP']; ?> | 
                    <input type="submit" value="More Info"> // WIP
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="test.html">Back</a>
    </body>
</html>

<?php $conn->close(); ?>
