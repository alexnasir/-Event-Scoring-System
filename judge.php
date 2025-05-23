<?php
session_start();
include 'db.php';

$errors = [];
$success_message = '';

if (!isset($_SESSION['judge_id'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judge_id = trim($_POST['judge_id']);
        if (empty($judge_id)) {
            $errors['judge_id'] = 'Judge ID is required.';
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
                    $errors['judge_id'] = 'Invalid Judge ID.';
                }
            } catch (PDOException $e) {
                $errors['general'] = 'Database error: ' . $e->getMessage();
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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <h1>Judge Portal</h1>
            <form method="post">
                <div class="form-group">
                    <label for="judge_id">Enter your Judge ID:</label>
                    <input type="text" name="judge_id" id="judge_id" value="<?php echo isset($_POST['judge_id']) ? htmlspecialchars($_POST['judge_id']) : ''; ?>">
                    <?php if (isset($errors['judge_id'])): ?>
                        <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['judge_id']; ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit">Proceed</button>
                <?php if (isset($errors['general'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </body>
    </html>
    <?php
} else {
    $judge_id = $_SESSION['judge_id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_POST['user_id'];
        // Fetch user name for personalized messages
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        $user_name = $user ? htmlspecialchars($user['name']) : 'Unknown User';

        if (isset($_POST['action']) && $_POST['action'] == 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM scores WHERE judge_id = ? AND user_id = ?");
                $stmt->execute([$judge_id, $user_id]);
                if ($stmt->rowCount() > 0) {
                    $success_message = 'Score for ' . $user_name . ' deleted successfully.';
                } else {
                    $errors[$user_id] = 'No score found to delete for ' . $user_name . '.';
                }
            } catch (PDOException $e) {
                $errors[$user_id] = 'Database error: ' . $e->getMessage();
            }
        } else {
            $points = trim($_POST['points']);
            if (empty($points)) {
                $errors[$user_id] = 'Points for ' . $user_name . ' are required.';
            } elseif (!is_numeric($points) || $points < 1 || $points > 100) {
                $errors[$user_id] = 'Points for ' . $user_name . ' must be between 1 and 100.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE points = ?");
                    $stmt->execute([$judge_id, $user_id, $points, $points]);
                    $success_message = 'Score for ' . $user_name . ' saved successfully.';
                } catch (PDOException $e) {
                    $errors[$user_id] = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
    try {
        $stmt = $pdo->query("SELECT id, name FROM users");
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errors['general'] = 'Database error: ' . $e->getMessage();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Score Users</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['judge_name']); ?></h1>
            <a href="logout.php" class="logout">Logout</a>
            <?php if ($success_message): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
            <?php endif; ?>
            <div class="card-container">
                <?php foreach ($users as $user): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT points FROM scores WHERE judge_id = ? AND user_id = ?");
                    $stmt->execute([$judge_id, $user['id']]);
                    $score = $stmt->fetch();
                    $points = $score ? $score['points'] : '';
                    ?>
                    <div class="card">
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                            <div class="form-group">
                                <label for="points_<?php echo $user['id']; ?>">Give Score:</label>
                                <input type="number" id="points_<?php echo $user['id']; ?>" name="points" value="<?php echo $points; ?>" min="1" max="100">
                                <?php if (isset($errors[$user['id']])): ?>
                                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors[$user['id']]; ?></p>
                                <?php endif; ?>
                            </div>
                            <button type="submit">Save</button>
                        </form>
                        <?php if ($score): ?>
                            <form method="post" class="delete-form">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>