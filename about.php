<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
< <div class="sidebar">
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
            <h1>About the Event Scoring System</h1>
            <div class="card-container">
                <div class="card">
                    <h2>Overview</h2>
                    <p>Hi, I’m Alex, an aspiring full-stack developer! The Event Scoring System has become my passion project, designed to simplify the process of managing scores for events like competitions and talent shows. I recently started working on this project, and I’m excited to build a tool that’s both efficient and user-friendly.</p>
                </div>
                <div class="card">
                    <h2>Project Journey</h2>
                    <p>I have began developing the Event Scoring System using XAMPP, JavaScript, CSS, and PHP. As of for now, I’m still in the early stages, gathering ideas and information to make this project more digitized and impactful. My goal is to create real-world solutions that make event scoring seamless for judges, admins, and participants.</p>
                </div>
                <div class="card">
                    <h2>My Skills</h2>
                    <p>I’m proficient in a variety of languages and frameworks that I plan to integrate into this project as it grows:</p>
                    <ul>
                        <li><strong>JavaScript:</strong> For dynamic and interactive features.</li>
                        <li><strong>CSS:</strong> To style the application with clean and responsive designs.</li>
                        <li><strong>PHP:</strong> For server-side logic and database interactions.</li>
                        <li><strong>React-Next.js Auth:</strong> For secure authentication using NextAuth.js, API routes, middleware, and Prisma integration.</li>
                        <li><strong>Tailwind:</strong> To streamline styling with utility-first CSS.</li>
                        <li><strong>TypeScript:</strong> For type-safe JavaScript development.</li>
                        <li><strong>Node.js:</strong> To build scalable backend services in the future.</li>
                        <li><strong>Flask & Python:</strong> For additional backend capabilities and scripting.</li>
                    </ul>
                </div>
                <div class="card">
                    <h2>Future Plans</h2>
                    <p>Role-Based Access Control: Implement separate dashboards and permissions for Admin, Judge, and User roles.</p>

                    <p>Automated Score Validation: Implement rules-based score validation with admin approval for flagged scores.</p>
<p>Real-Time Collaboration Tools: Add WebSocket for real-time judge collaboration with a chat interface.</p>
<p>Score Analytics Dashboard: Build an admin dashboard for score analytics with exportable reports (PDF/CSV).</p>
<p>Event Notifications: Add email/SMS notifications for judges and admins with opt-in settings.</p>
<p>Mobile Friendly: Ensure responsive design for seamless access on all devices, including phones and tablets.</p>

                    <p>My vision is to make the Event Scoring System a go-to tool for event organizers worldwide, with a focus on digitization and real-world usability. Stay tuned for updates!</p>
                </div>
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