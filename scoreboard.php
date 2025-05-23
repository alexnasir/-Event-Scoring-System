<?php
include 'db.php';

if (isset($_GET['ajax'])) {
    try {
        $stmt = $pdo->query("SELECT u.id, u.name, COALESCE(SUM(s.points), 0) as total_points 
                             FROM users u 
                             LEFT JOIN scores s ON u.id = s.user_id 
                             GROUP BY u.id, u.name 
                             ORDER BY total_points DESC");
        $rankings = $stmt->fetchAll();
        echo "<ol>";
        $rank = 1;
        foreach ($rankings as $user) {
            $class = '';
            if ($rank == 1) $class = 'gold';
            elseif ($rank == 2) $class = 'silver';
            elseif ($rank == 3) $class = 'bronze';
            echo "<li class='$class'>" . htmlspecialchars($user['name']) . " - " . $user['total_points'] . " points</li>";
            $rank++;
        }
        echo "</ol>";
    } catch (PDOException $e) {
        echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public Scoreboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateScoreboard() {
            $.get("scoreboard.php?ajax=1", function(data) {
                if (data.includes("Database error")) {
                    $("#scoreboard").html("<p class='error'>Error loading scoreboard.</p>");
                } else {
                    $("#scoreboard").html(data);
                }
            });
        }
        setInterval(updateScoreboard, 10000);
        $(document).ready(function() {
            updateScoreboard();
        });
    </script>
</head>
<body>
    <h1>Public Scoreboard</h1>
    <div id="scoreboard"></div>
</body>
</html>