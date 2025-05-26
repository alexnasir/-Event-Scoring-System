<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Scoring System</title>
    <link rel="stylesheet" href="style.css">
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
        <header class="hero">
            <div class="hero-content">
                <h1>Welcome to Event Scoring System</h1>
                <p class="subtitle">Your One-stop Event Scheduling Platform!</p>
                <a href="signup.php" class="cta-button">Get Started</a>
            </div>
        </header>

        <section class="features">
            <p><span>Event Scoring System</span> is a digital platform designed to streamline judging at competitions by automating score collection, calculation, and display, ensuring accuracy, transparency, and real-time updates for participants, organizers, and audiences.</p>
            <h2>Explore features on:</h2>
            <div class="feature-items">
                <div class="feature-item">
                    <div class="feature-image">Event Setup Add Judges</div>
                    <button class="feature-button" onclick="window.location.href='admin.php'">Admin Panel</button>
                </div>
                <div class="feature-items">
                <div class="feature-item">
                    <div class="feature-image">Explore Events</div>
                    <button class="feature-button" onclick="window.location.href='dashboard.php'">Upcoming Events</button>
                </div>
                <div class="feature-item">
                    <div class="feature-image">Attend an Event</div>
                    <button class="feature-button" onclick="window.location.href='dashboard.php'">Reserve Seat</button>
                </div>
                <div class="feature-item">
                    <div class="feature-image">Scoring in Action</div>
                    <button class="feature-button" onclick="window.location.href='judge.php'">Judge Portal</button>
                </div>
                <div class="feature-item">
                    <div class="feature-image">Score Display</div>
                    <button class="feature-button" onclick="window.location.href='scoreboard.php'">Scoreboard</button>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
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