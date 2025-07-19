<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Initialize variables
$errors = [];
$success_message = '';
$winners = [];
$payment_confirmed = false;

// Check payment status (simulated for this example)
if (isset($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'success') {
    $payment_confirmed = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve_seat'])) {
    $event = trim($_POST['event']);
    $name = trim($_POST['name']);
    $seat_number = trim($_POST['seat_number']);

    // Validation
    if (empty($event)) {
        $errors['event'] = 'Please select an event.';
    }
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }
    if (empty($seat_number)) {
        $errors['seat_number'] = 'Seat number is required.';
    } elseif (!is_numeric($seat_number) || $seat_number < 1 || $seat_number > 100) {
        $errors['seat_number'] = 'Seat number must be between 1 and 100.';
    }

    // Check payment status before reserving
    if (!$payment_confirmed) {
        $errors['payment'] = 'Payment must be completed before reserving a seat.';
    }

    // Check if seat is already taken for the selected event
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM seat_reservations WHERE event_name = ? AND seat_number = ?");
            $stmt->execute([$event, $seat_number]);
            $seat_taken = $stmt->fetchColumn();

            if ($seat_taken) {
                $errors['seat_number'] = 'This seat is already taken for the selected event.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO seat_reservations (event_name, user_name, seat_number) VALUES (?, ?, ?)");
                $stmt->execute([$event, $name, $seat_number]);
                $success_message = "Seat $seat_number for $event reserved successfully by $name!";
                // Reset payment status after successful reservation
                unset($_SESSION['payment_status']);
            }
        } catch (PDOException $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch top 3 winners based on total points from scores table
try {
    $stmt = $pdo->prepare("
        SELECT u.name, SUM(s.points) as total_points 
        FROM scores s 
        JOIN users u ON s.user_id = u.id 
        GROUP BY s.user_id, u.name 
        ORDER BY total_points DESC 
        LIMIT 3
    ");
    $stmt->execute();
    $winners = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors['database'] = 'Failed to load winners: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Rubik:wght@500&display=swap");

        .payment-card {
            width: 100%;
            border: none;
            border-radius: 15px;
            background: #fff;
            padding: 15px;
            margin-bottom: 1rem;
        }

        .payment-card .mpesa {
            background: #f3f4f6;
            padding: 5px 20px;
            border-radius: 20px;
            color: #8d9297;
            text-align: center;
        }

        .payment-card .mpesa.active {
            background: #545ebd;
            color: #fff;
        }

        .payment-card img {
            border-radius: 15px;
            height: 50px;
        }

        .payment-card h6 {
            font-size: 15px;
            margin-top: 10px;
        }

        /* Card styling for winners and explore */
        .winner-card, .explore-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .winner-card:hover, .explore-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .winner-card h2, .explore-card h2 {
            font-size: 1.6rem;
            color: #1a3c6d;
            margin-bottom: 1.2rem;
            font-weight: 600;
            border-bottom: 2px solid #ff6200;
            padding-bottom: 0.5rem;
        }

        /* Winner list styling */
        .winner-card ul {
            list-style: none;
            padding: 0;
        }

        .winner-card li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            font-size: 1.1rem;
            color: #333;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.3s ease;
        }

        .winner-card li:last-child {
            border-bottom: none;
        }

        .winner-card li:hover {
            background: #f0f4ff;
        }

        /* Action buttons (Congratulate, View Score Details) */
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: transform 0.2s ease, background 0.3s ease;
            cursor: pointer;
        }

        .congratulate-btn {
            background: #ff6200;
            color: #fff;
            border: none;
            margin-right: 0.5rem;
        }

        .congratulate-btn:hover {
            background: #e65c00;
            transform: scale(1.05);
        }

        .view-btn {
            background: #2563eb;
            color: #fff;
            border: none;
        }

        button {
            padding: 0.75rem 1.5rem;
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            width: fit-content;
            align-self: center;
        }

        button:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        button:disabled {
            background-color: #6b7280;
            cursor: not-allowed;
        }

        .view-btn:hover {
            background: #1d4ed8;
            transform: scale(1.05);
        }

        /* Explore buttons */
        .explore-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .nav-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
        }

        .nav-btn:hover::before {
            left: 0;
        }

        .nav-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .admin-btn {
            background: #2563eb;
        }

        .admin-btn:hover {
            background: #1d4ed8;
        }

        .judge-btn {
            background: #ff6200;
        }

        .judge-btn:hover {
            background: #e65c00;
        }

        .scoreboard-btn {
            background: #2c3e50;
        }

        .scoreboard-btn:hover {
            background: #1f2a3c;
        }

        .tall-card {
            flex: 1;
            min-width: 300px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .tall-card:hover {
            transform: translateY(-5px);
        }

        .tall-card h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .tall-card p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .save-btn {
            background-color: #2563eb;
        }

        .save-btn:hover {
            background-color: #1d4ed8;
        }

        .reserve-card {
            flex: 1;
            min-width: 300px;
        }

        .reserve-card form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .reserve-card select, .reserve-card input {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .reserve-card select:focus, .reserve-card input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .disclaimer {
            width: 100%;
            background-color: #fef3c7;
            color: #d97706;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            text-align: center;
            margin: 1rem 0;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .disclaimer p {
            margin: 0;
        }

        .congratulation-message {
            margin-top: 1rem;
            display: none;
        }

        .congratulation-message .message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 1rem;
            text-align: center;
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
                <a href="/history.php">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-label">History</span>
                </a>
            </li>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <li class="sidebar-item">
                    <a href="/signup.php">
                        <i class="fas fa-user-plus"></i>
                        <span class="sidebar-label">Signup</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="/login.php">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="sidebar-label">Login</span>
                    </a>
                </li>
            <?php else: ?>
                <li class="sidebar-item">
                    <a href="/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="sidebar-label">Logout</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="main-content">
        <div class="container">
            <h1>Dashboard</h1>
            <div class="disclaimer">
                <p>Disclaimer: Some functions (e.g., Settings, Notifications and Some Buttons) are still being developed. Stay tuned for updates. Coming soon!</p>
            </div>
            <div class="card-container dashboard-grid">
                <div class="stacked-cards">
                    <div class="card winner-card">
                        <h2>Leading Winners</h2>
                        <ul>
                            <?php foreach ($winners as $index => $winner): ?>
                                <li>
                                    <?php echo htmlspecialchars($winner['name']) . ' - ' . htmlspecialchars($winner['total_points']); ?>
                                    <button class="action-btn congratulate-btn">Congratulate</button>
                                    <button class="action-btn view-btn">View Score Details</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="congratulation-message"></div>
                    </div>
                    <div class="card explore-card">
                        <h2>Explore Event Scoring</h2>
                        <div class="explore-buttons">
                            <a href="/admin.php" class="nav-btn admin-btn">Admin Panel</a>
                            <a href="/judge.php" class="nav-btn judge-btn">Judge Portal</a>
                            <a href="/scoreboard.php" class="nav-btn scoreboard-btn">Scoreboard</a>
                        </div>
                    </div>
                </div>
                <div class="card tall-card">
                    <h2>Upcoming Events</h2>
                    <p>Event: Genz Fashion Show <br>Date: May 30, 2025<br>Fee: Ksh.250</p>
                    <p>Event: WestSide Talent Show <br>Date: June 15, 2025<br>Fee: Ksh.150</p>
                    <p>Event: Tech Explorer <br>Date: December 25, 2025<br>Fee: Free</p>
                </div>
                <div class="card reserve-card">
                    <h2>Reserve a Seat</h2>
                    <?php if ($success_message): ?>
                        <p class="message"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars(implode(' ', $errors)); ?></p>
                    <?php endif; ?>
                    <form method="post" class="reserve-form">
                        <input type="hidden" name="reserve_seat" value="1">
                        <div class="form-group">
                            <label for="event">Select Event:</label>
                            <select name="event" id="event">
                                <option value="">-- Select an Event --</option>
                                <option value="Genz Fashion Show" <?php echo isset($_POST['event']) && $_POST['event'] == 'Genz Fashion Show' ? 'selected' : ''; ?>>Genz Fashion Show</option>
                                <option value="WestSide Talent Show" <?php echo isset($_POST['event']) && $_POST['event'] == 'WestSide Talent Show' ? 'selected' : ''; ?>>WestSide Talent Show</option>
                                <option value="Tech Explorer" <?php echo isset($_POST['event']) && $_POST['event'] == 'Tech Explorer' ? 'selected' : ''; ?>>Tech Explorer</option>
                            </select>
                            <?php if (isset($errors['event'])): ?>
                                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['event']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            <?php if (isset($errors['name'])): ?>
                                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['name']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="seat_number">Seat Number (1-100):</label>
                            <input type="number" name="seat_number" id="seat_number" min="1" max="100" value="<?php echo isset($_POST['seat_number']) ? htmlspecialchars($_POST['seat_number']) : ''; ?>">
                            <?php if (isset($errors['seat_number'])): ?>
                                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['seat_number']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="payment-card">
                          
                            <div class="media mt-3 pl-2">
                                <img src="./images/mpesa.png" />
                                </div>
                                <div class="media-body">
                                    <h6 class="mt-1">Enter Amount & Number</h6>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label دهد. for="inputAddress" class="form-label">Amount</label>
                                        <input type="text" class="form-control" name="amount" placeholder="Enter Amount">
                                    </div>
                                    <div class="col-12">
                                        <label for="inputAddress2" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone" placeholder="Enter Phone Number">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success" name="submit" value="submit">Pay Now</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="save-btn" <?php echo $payment_confirmed ? '' : 'disabled'; ?>>Reserve Seat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
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

            $('.reserve-form').on('submit', function(e) {
                $(this).find('.error').hide();
                let hasError = false;

                const event = $(this).find('#event').val();
                const name = $(this).find('#name').val().trim();
                const seatNumber = $(this).find('#seat_number').val().trim();
                const amount = $(this).find('input[name="amount"]').val().trim();
                const phone = $(this).find('input[name="phone"]').val().trim();

                if (!event) {
                    $(this).find('#event').next('.error').text('Please select an event.').show();
                    hasError = true;
                }
                if (!name) {
                    $(this).find('#name').next('.error').text('Name is required.').show();
                    hasError = true;
                }
                if (!seatNumber || seatNumber < 1 || seatNumber > 100) {
                    $(this).find('#seat_number').next('.error').text('Seat number must be between 1 and 100.').show();
                    hasError = true;
                }
                if (!amount || isNaN(amount) || amount <= 0) {
                    $(this).find('input[name="amount"]').next('.error').text('Valid amount is required.').show();
                    hasError = true;
                }
                if (!phone || !/^\d{10}$/.test(phone)) {
                    $(this).find('input[name="phone"]').next('.error').text('Valid phone number is required.').show();
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }

                $(this).find('#event, #name, #seat_number, input[name="amount"], input[name="phone"]').on('input change', function() {
                    $(this).next('.error').hide();
                });
            });

            // Handle congratulate button click
            $('.congratulate-btn').on('click', function() {
                const liText = $(this).parent().text();
                const name = liText.split(' - ')[0].trim();

                const message = `<p class="message"><i class="fas fa-check-circle"></i> Congratulations, ${name}!</p>`;
                const $messageArea = $(this).closest('.winner-card').find('.congratulation-message');
                $messageArea.html(message).show();

                setTimeout(() => {
                    $messageArea.fadeOut(500, function() {
                        $(this).empty();
                    });
                }, 5000);
            });

            function setMessageTimeout() {
                $('.message, .error').each(function() {
                    if ($(this).is(':visible')) {
                        setTimeout(() => {
                            $(this).fadeOut(500);
                        }, 5000);
                    }
                });
            }

            setMessageTimeout();
            $('.reserve-form').on('submit', function() {
                setTimeout(setMessageTimeout, 100);
            });
        });
    </script>
</body>
</html>