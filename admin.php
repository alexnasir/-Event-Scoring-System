<?php
include 'db.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judge_id = trim($_POST['judge_id']);
    $display_name = trim($_POST['display_name']);
    
    if (empty($judge_id)) {
        $errors['judge_id'] = 'Judge ID is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $judge_id)) {
        $errors['judge_id'] = 'Judge ID must be alphanumeric.';
    }
    
    if (empty($display_name)) {
        $errors['display_name'] = 'Display name is required.';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $display_name)) {
        $errors['display_name'] = 'Display name must contain only letters and spaces.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM judges WHERE id = ?");
            $stmt->execute([$judge_id]);
            if ($stmt->fetchColumn() > 0) {
                $errors['judge_id'] = 'Judge ID already exists.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO judges (id, display_name) VALUES (?, ?)");
                $stmt->execute([$judge_id, $display_name]);
                $success_message = 'Judge ' . htmlspecialchars($display_name) . ' added successfully.';
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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <form method="post">
            <div class="form-group">
                <label for="judge_id">Judge ID:</label>
                <input type="text" name="judge_id" id="judge_id" value="<?php echo isset($_POST['judge_id']) ? htmlspecialchars($_POST['judge_id']) : ''; ?>">
                <?php if (isset($errors['judge_id'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['judge_id']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="display_name">Display Name:</label>
                <input type="text" name="display_name" id="display_name" value="<?php echo isset($_POST['display_name']) ? htmlspecialchars($_POST['display_name']) : ''; ?>">
                <?php if (isset($errors['display_name'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['display_name']; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit">Add Judge</button>
            <?php if ($success_message): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>