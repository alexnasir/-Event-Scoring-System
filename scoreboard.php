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

// Prepare data for the chart
$labels = array_map(function($score) { return $score['name']; }, $scores);
$data = array_map(function($score) { return $score['average_score']; }, $scores);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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
        <div class="card-container">
            <div class="card chart-card">
                <h2>Score Trends</h2>
                <div class="chart-controls">
                    <label for="chart-type">Chart Type:</label>
                    <select id="chart-type">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                        <option value="scatter">Scatter</option>
                    </select>
                </div>
                <canvas id="scoreChart" width="400" height="200"></canvas>
            </div>
            <div class="card scoreboard-card" id="scoreboard">
                <h2>Rankings</h2>
                <ol>
                    <?php foreach ($scores as $index => $score): ?>
                        <li class="<?php echo $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')); ?>">
                            <?php echo htmlspecialchars($score['name']) . ': ' . number_format($score['average_score'], 2); ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
    <script>
        // Initial chart setup
        const labels = <?php echo json_encode($labels); ?>;
        const data = <?php echo json_encode($data); ?>;

        const ctx = document.getElementById('scoreChart').getContext('2d');
        let chartType = $('#chart-type').val() || 'line';

        const chartConfig = {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: 'Average Score',
                    data: data,
                    borderColor: '#ff6200',
                    backgroundColor: chartType === 'scatter' ? '#ff6200' : 'rgba(255, 98, 0, 0.2)',
                    fill: chartType === 'line',
                    tension: chartType === 'line' ? 0.4 : 0,
                    pointRadius: chartType === 'scatter' ? 5 : (chartType === 'line' ? 5 : 0),
                    pointBackgroundColor: '#ff6200'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Users'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toFixed(2);
                                return label;
                            }
                        }
                    }
                }
            }
        };

        let scoreChart = new Chart(ctx, chartConfig);

        $(document).ready(function() {
            // Chart type toggle
            $('#chart-type').on('change', function() {
                chartType = $(this).val();
                scoreChart.destroy();
                chartConfig.type = chartType;
                chartConfig.data.datasets[0].fill = chartType === 'line';
                chartConfig.data.datasets[0].tension = chartType === 'line' ? 0.4 : 0;
                chartConfig.data.datasets[0].pointRadius = chartType === 'scatter' ? 5 : (chartType === 'line' ? 5 : 0);
                chartConfig.data.datasets[0].backgroundColor = chartType === 'scatter' ? '#ff6200' : 'rgba(255, 98, 0, 0.2)';
                scoreChart = new Chart(ctx, chartConfig);
            });

            function updateScoreboardAndChart() {
                $.ajax({
                    url: 'scoreboard.php',
                    method: 'GET',
                    success: function(data) {
                        // Update scoreboard
                        $('#scoreboard').html($(data).find('#scoreboard').html());

                        // Update chart data
                        const newLabels = $(data).find('script').first().text().match(/labels = (.*?);/)[1];
                        const newData = $(data).find('script').first().text().match(/data = (.*?);/)[1];
                        scoreChart.data.labels = JSON.parse(newLabels);
                        scoreChart.data.datasets[0].data = JSON.parse(newData);
                        scoreChart.update();
                    }
                });
            }

            setInterval(updateScoreboardAndChart, 5000);

            $('.navbar-toggle').on('click', function() {
                $('.navbar-menu').toggleClass('active');
            });
        });
    </script>
</body>
</html>