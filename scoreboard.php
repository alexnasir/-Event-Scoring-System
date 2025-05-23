<?php
session_start();
include 'db.php';

try {
    $stmt = $pdo->query("
        SELECT u.name, AVG(s.points) as average_score
        FROM users u
        LEFT JOIN scores s ON u.id = s.user_id
        GROUP BY u.id, u.name
        ORDER BY average_score DESC
    ");
    $scores = $stmt->fetchAll();
} catch (PDOException $e) {
    echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scoreboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">Event Scoring System</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-menu">
            <div class="nav-item"><a href="index.php">Home</a></div>
            <div class="nav-item"><a href="admin.php">Admin Panel</a></div>
            <div class="nav-item"><a href="judge.php">Judge Portal</a></div>
            <div class="nav-item"><a href="scoreboard.php" class="active">Scoreboard</a></div>
            <?php if (isset($_SESSION['judge_id'])): ?>
                <div class="nav-item"><a href="logout.php" class="logout">Logout</a></div>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="container">
        <h1>Scoreboard</h1>
        <div id="scoreboard">
            <ol>
                <?php foreach ($scores as $index => $score): ?>
                    <li class="<?php echo $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')); ?>">
                        <?php echo htmlspecialchars($score['name']) . ': ' . number_format($score['average_score'], 2); ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            function updateScoreboard() {
                $.ajax({
                    url: 'scoreboard.php',
                    method: 'GET',
                    success: function(data) {
                        $('#scoreboard').html($(data).find('#scoreboard').html());
                    }
                });
            }
            setInterval(updateScoreboard, 5000);

            $('.navbar-toggle').on('click', function() {
                $('.navbar-menu').toggleClass('active');
            });
        });
    </script>
</body>
</html>