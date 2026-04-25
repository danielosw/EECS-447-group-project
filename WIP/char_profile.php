<?php

    // Connect to MySQL server
    $conn = new mysqli('localhost:3307', 'test', 'test', 'testdb');
    if ($conn->connect_error)
       die('Could not connect: ' . $conn->connect_error);

    // Send query for specific character
    $char_name = $_POST['charname'];
        // $query = "SELECT * FROM CHARACTERS WHERE NAME = '$char_name'";
        // $result = $conn->query($query);
    $stmt = $conn->prepare("SELECT * FROM CHARACTERS WHERE NAME = ?");
    $stmt->bind_param("s", $char_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $character = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale1.0">
        <title><?php echo $character['NAME']; ?> - Character Profile</title>
    </head>

    <body>
        <?php if ($result->num_rows == 0): ?>
            <p>No Results Found.</p>
        <?php else: ?>
            <h1>
                <?php echo $character['NAME']; ?>
            </h1>

            <p>
                Role: <?php echo $character['ROLE']; ?> <br>
                Difficulty: <?php echo $character['DIFFICULTY']; ?> <br>
                Win rate: <?php 
                $query = "SELECT (SELECT count(*) FROM MATCH_STATS WHERE CHARACTER_ID = (SELECT CHARACTER_ID FROM CHARACTERS WHERE NAME = ?) AND WIN_LOSS = 'Win')/(SELECT count(*) FROM MATCH_STATS WHERE CHARACTER_ID = (SELECT CHARACTER_ID FROM CHARACTERS WHERE NAME = ?)) AS 'character win rate'";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $character['NAME'], $character['NAME']);
                $stmt->execute();
                $win_rate_result = $stmt->get_result()->fetch_assoc();
                echo $win_rate_result['character win rate'];
                ?>
            </p>
        <?php endif; ?>

        <a href="test.html">Back</a>
    </body>
</html>

<?php $conn->close(); ?>
