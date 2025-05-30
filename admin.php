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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="form-page">
<div class="sidebar">
        <div class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="sidebar-menu">
        <li class="sidebar-item">
                <a href="/dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-label">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/index.php">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-label">Home</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/admin.php">
                    <i class="fas fa-user-shield"></i>
                    <span class="sidebar-label">Admin Panel</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/judge.php">
                    <i class="fas fa-gavel"></i>
                    <span class="sidebar-label">Judge Portal</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/scoreboard.php">
                    <i class="fas fa-trophy"></i>
                    <span class="sidebar-label">Scoreboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/about.php">
                    <i class="fas fa-info-circle"></i>
                    <span class="sidebar-label">About</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/contact.php">
                    <i class="fas fa-envelope"></i>
                    <span class="sidebar-label">Contact</span>
                </a>
            </li>
    
            <li class="sidebar-item">
                <a href="/generate_report.php">
                    <i class="fas fa-file-alt"></i>
                    <span class="sidebar-label">Generate Report</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span class="sidebar-label">Settings</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#">
                    <i class="fas fa-bell"></i>
                    <span class="sidebar-label">Notification</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="/history.php">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-label">History</span>
                </a>
            </li>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li class="sidebar-item">
                    <a href="signup.php">
                        <i class="fas fa-user-plus"></i>
                        <span class="sidebar-label">Signup</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="sidebar-label">Login</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="sidebar-item">
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="sidebar-label">Logout</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="main-content">
        <div class="container form-container">
            <h1>Admin Panel</h1>
            <form method="post" id="add-judge-form" class="modern-form">
                <div class="form-group">
                    <label for="judge_id">Judge ID</label>
                    <input type="text" name="judge_id" id="judge_id" value="<?php echo isset($_POST['judge_id']) ? htmlspecialchars($_POST['judge_id']) : ''; ?>">
                    <p class="error" style="display: none;"></p>
                    <?php if (isset($errors['judge_id'])): ?>
                        <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['judge_id']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="display_name">Display Name</label>
                    <input type="text" name="display_name" id="display_name" value="<?php echo isset($_POST['display_name']) ? htmlspecialchars($_POST['display_name']) : ''; ?>">
                    <p class="error" style="display: none;"></p>
                    <?php if (isset($errors['display_name'])): ?>
                        <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['display_name']; ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="gradient-btn">Add Judge</button>
                <?php if ($success_message): ?>
                    <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
                <?php endif; ?>
                <?php if (isset($errors['general'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#add-judge-form').on('submit', function(e) {
                $('.error').hide();
                let hasError = false;
                const judgeId = $('#judge_id').val().trim();
                const displayName = $('#display_name').val().trim();
                if (!judgeId) {
                    $('#judge_id').next('.error').text('Judge ID is required.').show();
                    hasError = true;
                }
                if (!displayName) {
                    $('#display_name').next('.error').text('Display name is required.').show();
                    hasError = true;
                }
                if (hasError) e.preventDefault();
                $('#judge_id, #display_name').on('input', function() {
                    $(this).next('.error').hide();
                });
            });

            $('.sidebar-toggle').on('click', function() {
                $('.sidebar').toggleClass('collapsed');
            });

            if ($(window).width() <= 768) {
                $('.sidebar').addClass('collapsed');
            }

            $(window).resize(function() {
                if ($(window).width() <= 768) {
                    $('.sidebar').addClass('collapsed');
                } else {
                    $('.sidebar').removeClass('collapsed');
                }
            });
        });
    </script>
</body>
</html>