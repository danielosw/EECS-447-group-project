<?php

// Connect to MySQL server, select database
        $conn = new mysqli('localhost:3307', 'test', 'test', 'testdb');
        if ($conn ->connect_error)
               die('Could not connect: ' . $conn->connect_error);


// Send SQL query
        $player_id = $_POST['player_id'];
        $query = "SELECT (SELECT count(*) FROM  MATCH_STATS WHERE PLAYER_ID=$player_id AND WIN_OR_LOSE = 'Win')/(SELECT count(*) FROM MATCH_STATS  WHERE PLAYER_ID = $player_id ) AS 'win loss';";
        $result = $conn -> query($query);

// Print results in HTML
        echo "<table>\n";
        while ($line = $result->fetch_assoc()) {
                echo "\t<tr>\n";
                foreach ($line as $col_value) {
                        echo "\t\t<td>$col_value</td>\n";
                }
                echo "\t</tr>\n";
        }
        echo "</table>\n";

//      echo "Number of fields: ".mysql_num_fields($result)."<br>";
//      echo "Number of records: ".mysql_num_rows($result)."<br>";


// Close connection
        $conn->close();
?>
