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
    <nav class="navbar">
        <div class="navbar-brand">Event Scoring System</div>
        <button class="navbar-toggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-menu">
            <div class="nav-item"><a href="index.php" class="active">Home</a></div>
            <div class="nav-item"><a href="admin.php">Admin Panel</a></div>
            <div class="nav-item"><a href="judge.php">Judge Portal</a></div>
            <div class="nav-item"><a href="scoreboard.php">Scoreboard</a></div>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="nav-item"><a href="signup.php">Sign Up</a></div>
                <div class="nav-item"><a href="login.php">Login</a></div>
            <?php else: ?>
                <div class="nav-item"><a href="logout.php" class="logout">Logout</a></div>
            <?php endif; ?>
        </ul>
    </nav>

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
                <div class="feature-image">Event Setup</div>
                <button class="feature-button" onclick="window.location.href='admin.php'">Admin Panel</button>
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

    <footer class="footer">
        <div class="footer-links">
            <a href="#">©All Rights Reserved</a>
            <a href="#">Alex Nasiali</a>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            $('.navbar-toggle').on('click', function() {
                $('.navbar-menu').toggleClass('active');
            });
        });
    </script>
</body>
</html>