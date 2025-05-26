<?php
session_start();
include 'db.php';

// Fetch score history
$stmt = $pdo->query("SELECT sh.id, sh.judge_id, j.display_name AS judge_name, sh.user_id, u.name AS user_name, sh.action, sh.points, sh.action_timestamp
                     FROM score_history sh
                     JOIN judges j ON sh.judge_id = j.id
                     JOIN users u ON sh.user_id = u.id
                     ORDER BY sh.action_timestamp DESC");
$score_history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Score History</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-toggle">
        <i class="fas fa-bars"></i>
    </div>
    <ul class="sidebar-menu">
        <li class="sidebar-item">
            <a href="/dashboard.php">
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
            <a href="/history.php" class="active">
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
    <div class="container">
        <h1>Score History</h1>
        <div class="card">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 1rem;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Judge</th>
                        <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">User</th>
                        <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Action</th>
                        <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Points</th>
                        <th style="border: 1px solid #ddd; padding: 0.5rem; text-align: left;">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($score_history)): ?>
                        <tr>
                            <td colspan="5" style="border: 1px solid #ddd; padding: 0.5rem; text-align: center;">No score history available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($score_history as $entry): ?>
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 0.5rem;"><?php echo htmlspecialchars($entry['judge_name']); ?></td>
                                <td style="border: 1px solid #ddd; padding: 0.5rem;"><?php echo htmlspecialchars($entry['user_name']); ?></td>
                                <td style="border: 1px solid #ddd; padding: 0.5rem;"><?php echo $entry['action']; ?></td>
                                <td style="border: 1px solid #ddd; padding: 0.5rem;"><?php echo $entry['points']; ?></td>
                                <td style="border: 1px solid #ddd; padding: 0.5rem;"><?php echo $entry['action_timestamp']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.sidebar-toggle').on('click', function() {
            $('.sidebar').toggleClass('collapsed');
            $('.main-content').toggleClass('collapsed');
        });

        if ($(window).width() <= 768) {
            $('.sidebar').addClass('collapsed');
            $('.main-content').addClass('collapsed');
        }

        $(window).resize(function() {
            if ($(window).width() <= 768) {
                $('.sidebar').addClass('collapsed');
                $('.main-content').addClass('collapsed');
            } else {
                $('.sidebar').removeClass('collapsed');
                $('.main-content').removeClass('collapsed');
            }
        });
    });
</script>
</body>
</html>