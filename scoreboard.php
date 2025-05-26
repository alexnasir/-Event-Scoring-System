<?php
include 'db.php';

$stmt = $pdo->query("SELECT u.id, u.name, COALESCE(SUM(s.points), 0) as total_points
                     FROM users u
                     LEFT JOIN scores s ON u.id = s.user_id
                     GROUP BY u.id, u.name
                     ORDER BY total_points DESC");
$users = $stmt->fetchAll();

$labels = array_column($users, 'name');
$data = array_column($users, 'total_points');

// Prepare data for scatter chart (requires x, y coordinates)
$scatterData = array_map(function($label, $points, $index) {
    return ['x' => $index + 1, 'y' => $points];
}, $labels, $data, array_keys($labels));
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <div class="container">
            <div class="card-container">
                <div class="chart-card">
                    <h2>Scoreboard</h2>
                    <div class="chart-controls">
                        <label for="chart-type">Select Chart Type:</label>
                        <select id="chart-type" name="chart-type">
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                            <option value="scatter">Scatter Chart</option>
                        </select>
                    </div>
                    <canvas id="scoreChart"></canvas>
                </div>
                <div class="scoreboard-card">
                    <h2>Ranking</h2>
                    <div id="scoreboard">
                        <ol>
                            <?php foreach ($users as $index => $user): ?>
                                <li class="<?php echo $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')); ?>">
                                    <?php echo htmlspecialchars($user['name']) . ": " . $user['total_points'] . " points"; ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const ctx = document.getElementById('scoreChart').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels); ?>,
                    datasets: [{
                        label: 'Total Points',
                        data: <?php echo json_encode($data); ?>,
                        backgroundColor: 'rgba(98, 0, 234, 0.6)',
                        borderColor: 'rgba(98, 0, 234, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            $('#chart-type').on('change', function() {
                const chartType = $(this).val();
                chart.destroy();

                if (chartType === 'scatter') {
                    chart = new Chart(ctx, {
                        type: 'scatter',
                        data: {
                            datasets: [{
                                label: 'Total Points',
                                data: <?php echo json_encode($scatterData); ?>,
                                backgroundColor: 'rgba(98, 0, 234, 0.6)',
                                borderColor: 'rgba(98, 0, 234, 1)',
                                borderWidth: 1,
                                pointRadius: 5
                            }]
                        },
                        options: {
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Rank'
                                    },
                                    beginAtZero: true
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Points'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } else {
                    chart = new Chart(ctx, {
                        type: chartType,
                        data: {
                            labels: <?php echo json_encode($labels); ?>,
                            datasets: [{
                                label: 'Total Points',
                                data: <?php echo json_encode($data); ?>,
                                backgroundColor: 'rgba(98, 0, 234, 0.6)',
                                borderColor: 'rgba(98, 0, 234, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });

            function updateScoreboard() {
                $.ajax({
                    url: 'scoreboard.php',
                    method: 'GET',
                    success: function(data) {
                        const scoreboard = $(data).find('#scoreboard').html();
                        $('#scoreboard').html(scoreboard);
                    }
                });
            }

            setInterval(updateScoreboard, 10000);

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