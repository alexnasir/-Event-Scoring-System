<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judge_id = trim($_POST['judge_id']);
    $display_name = trim($_POST['display_name']);
    
    if (empty($judge_id) || empty($display_name)) {
        echo '<p class="error">All fields are required.</p>';
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $judge_id)) {
        echo '<p class="error">Judge ID must be alphanumeric.</p>';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $display_name)) {
        echo '<p class="error">Display name must contain only letters and spaces.</p>';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM judges WHERE id = ?");
            $stmt->execute([$judge_id]);
            if ($stmt->fetchColumn() > 0) {
                echo '<p class="error">Judge ID already exists.</p>';
            } else {
                $stmt = $pdo->prepare("INSERT INTO judges (id, display_name) VALUES (?, ?)");
                $stmt->execute([$judge_id, $display_name]);
                echo '<p class="message">Judge added successfully.</p>';
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <form method="post">
        <label for="judge_id">Judge ID:</label>
        <input type="text" name="judge_id" required>
        <label for="display_name">Display Name:</label>
        <input type="text" name="display_name" required>
        <button type="submit">Add Judge</button>
    </form>
</body>
</html>