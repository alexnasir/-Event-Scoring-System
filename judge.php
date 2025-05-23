<?php
session_start();
include 'db.php';

if (!isset($_SESSION['judge_id'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judge_id = trim($_POST['judge_id']);
        if (empty($judge_id)) {
            echo '<p class="error">Judge ID is required.</p>';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT display_name FROM judges WHERE id = ?");
                $stmt->execute([$judge_id]);
                if ($row = $stmt->fetch()) {
                    $_SESSION['judge_id'] = $judge_id;
                    $_SESSION['judge_name'] = $row['display_name'];
                    header("Location: judge.php");
                    exit;
                } else {
                    echo '<p class="error">Invalid Judge ID.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Judge Login</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Judge Portal</h1>
        <form method="post">
            <label for="judge_id">Enter your Judge ID:</label>
            <input type="text" name="judge_id" required>
            <button type="submit">Proceed</button>
        </form>
    </body>
    </html>
    <?php
} else {
    $judge_id = $_SESSION['judge_id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_POST['user_id'];
        $points = $_POST['points'];
        if (!is_numeric($points) || $points < 1 || $points > 100) {
            echo '<p class="error">Points must be between 1 and 100.</p>';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE points = ?");
                $stmt->execute([$judge_id, $user_id, $points, $points]);
                echo '<p class="message">Score saved successfully.</p>';
            } catch (PDOException $e) {
                echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
            }
        }
    }
    try {
        $stmt = $pdo->query("SELECT id, name FROM users");
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo '<p class="error">Database error: ' . $e->getMessage() . '</p>';
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Score Users</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['judge_name']); ?></h1>
        <a href="logout.php" class="logout">Logout</a>
        <?php foreach ($users as $user): ?>
            <?php
            $stmt = $pdo->prepare("SELECT points FROM scores WHERE judge_id = ? AND user_id = ?");
            $stmt->execute([$judge_id, $user['id']]);
            $score = $stmt->fetch();
            $points = $score ? $score['points'] : '';
            ?>
            <form method="post">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <label><?php echo htmlspecialchars($user['name']); ?>: </label>
                <input type="number" name="points" value="<?php echo $points; ?>" min="1" max="100" required>
                <button type="submit">Save</button>
            </form>
        <?php endforeach; ?>
    </body>
    </html>
    <?php
}
?>