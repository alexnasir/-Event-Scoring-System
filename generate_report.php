<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch leading winners from the database
try {
    $stmt = $pdo->query("SELECT u.id, u.name, COALESCE(SUM(s.points), 0) as total_points
                         FROM users u
                         LEFT JOIN scores s ON u.id = s.user_id
                         GROUP BY u.id, u.name
                         ORDER BY total_points DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Handle PDF generation request
if (isset($_GET['download_pdf'])) {

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Generate HTML for PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <title>Leaderboard Report</title>
        <style>
            body { font-family: "Inter", sans-serif; margin: 20px; }
            h1 { color: #1a3c6d; text-align: center; }
            p { text-align: center; color: #333; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f4f4f4; font-weight: 600; }
            tr:nth-child(even) { background-color: #f9f9f9; }
        </style>
    </head>
    <body>
        <h1>Leaderboard Report</h1>
        <p>Generated on: ' . date('Y-m-d') . '</p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>';
    
    if (empty($users)) {
        $html .= '<tr><td colspan="3" style="text-align:center;">No data available.</td></tr>';
    } else {
        foreach ($users as $index => $user) {
            $html .= '<tr><td>' . ($index + 1) . '</td><td>' . htmlspecialchars($user['name']) . '</td><td>' . $user['total_points'] . '</td></tr>';
        }
    }
    
    $html .= '
            </tbody>
        </table>
    </body>
    </html>';

    // Load HTML and generate PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('leaderboard_report.pdf', ['Attachment' => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: 600;
        }

        .cta-button {
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        .message, .error {
            margin-top: 1rem;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 1rem;
            text-align: center;
        }

        .message {
            background-color: #d1fae5;
            color: #065f46;
        }

        .error {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .sidebar {
            width: 250px;
            background: #1a3c6d;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar-toggle {
            padding: 1rem;
            cursor: pointer;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.2s ease;
        }

        .sidebar-item a:hover {
            background: #2563eb;
        }

        .sidebar-label {
            margin-left: 1rem;
        }

        .sidebar.collapsed .sidebar-label {
            display: none;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 60px;
        }

        h1 {
            font-size: 2rem;
            color: #1a3c6d;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
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
            <a href="#">
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
        <h1>Generate Report</h1>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo $user['total_points']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="?download_pdf=1" class="cta-button" id="generateReport">Download Report as PDF</a>
            <?php if (isset($error)): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <p id="successMessage" class="message" style="display: none;"><i class="fas fa-check-circle"></i> Report Generated Successfully.</p>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#generateReport').on('click', function() {
            $('#successMessage').fadeIn().delay(3000).fadeOut();
        });

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