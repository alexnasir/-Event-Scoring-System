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
        <div class="navbar-container">
            <div class="navbar-brand">
                <a href="/index.php">Event Scoring</a>
            </div>
            <div class="navbar-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="navbar-menu">
                <li class="navbar-item"><a href="/admin.php">Admin Panel</a></li>
                <li class="navbar-item"><a href="/judge.php">Judge Portal</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="navbar-item"><a href="signup.php">SignUp</a></li>
                    <li class="navbar-item"><a href="login.php">Login</a></li>
                <?php else: ?>
                    <li class="navbar-item"><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

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
                <a href="#about">
                    <i class="fas fa-info-circle"></i>
                    <span class="sidebar-label">About</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#contact">
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

        <section id="about" class="about-section">
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
        </section>

        <section id="contact" class="contact-section">
            <div class="container">
                <h1>Contact Us</h1>
                <div class="card-container">
                    <div class="card">
                        <h2>Get in Touch</h2>
                        <p>For support, inquiries, or feedback, please reach out to us:</p>
                        <p><strong>Email:</strong> support@eventscoring.com</p>
                        <p><strong>Phone:</strong> +254 713 388 680</p>
                        <p><strong>Address:</strong> Muratha Street, Nairobi, Kenya</p>
                    </div>
                    <div class="card">
                        <h2>Follow Us</h2>
                        <div class="social-icons">
                            <a href="https://twitter.com/AlexNasial2303" target="_blank" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://facebook.com/alex_nasi_life" target="_blank" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://instagram.com/alex_nasi_life" target="_blank" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.linkedin.com/in/alex-nasiali-219067333/" target="_blank" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="mailto:alexnasiali45@gmail.com" aria-label="Email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('.sidebar-toggle').on('click', function() {
                $('.sidebar').toggleClass('collapsed');
                $('.main-content').toggleClass('collapsed');
            });

            // Navbar toggle for mobile
            $('.navbar-toggle').on('click', function() {
                $('.navbar-menu').toggleClass('active');
            });

            // Handle responsive sidebar
            if ($(window).width() <= 768) {
                $('.sidebar').addClass('collapsed');
                $('.main-content').addClass('collapsed');
                $('.navbar-menu').removeClass('active');
            } else {
                $('.sidebar').removeClass('collapsed');
                $('.main-content').removeClass('collapsed');
            }

            $(window).resize(function() {
                if ($(window).width() <= 768) {
                    $('.sidebar').addClass('collapsed');
                    $('.main-content').addClass('collapsed');
                    $('.navbar-menu').removeClass('active');
                } else {
                    $('.sidebar').removeClass('collapsed');
                    $('.main-content').removeClass('collapsed');
                    $('.navbar-menu').removeClass('active');
                }
            });

            // Smooth scroll for About and Contact links
            $('a[href*="#"]').on('click', function(e) {
                if (this.hash !== "") {
                    e.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - 60
                    }, 800);
                }
            });
        });
    </script>
</body>
</html>

