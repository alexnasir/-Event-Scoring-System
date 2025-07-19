<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Create payments table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    payment_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    payment_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Initialize variables
$errors = [];
$success_message = '';
$winners = [];
$payment_confirmed = false;

// Check payment status for the current user and event
if (isset($_SESSION['user_id']) && isset($_POST['event'])) {
    $stmt = $pdo->prepare("SELECT payment_status FROM payments WHERE user_id = ? AND event_name = ? AND payment_status = 'success' ORDER BY payment_timestamp DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id'], $_POST['event']]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($payment && $payment['payment_status'] === 'success') {
        $payment_confirmed = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) { // Handle payment submission
        $event = trim($_POST['event']);
        $name = trim($_POST['name']);
        $seat_number = trim($_POST['seat_number']);
        $amount = trim($_POST['amount']);
        $phone = trim($_POST['phone']);

        // Validation
        if (empty($event)) $errors['event'] = 'Please select an event.';
        if (empty($name)) $errors['name'] = 'Name is required.';
        if (empty($seat_number) || !is_numeric($seat_number) || $seat_number < 1 || $seat_number > 100) $errors['seat_number'] = 'Seat number must be between 1 and 100.';
        if (empty($amount) || !is_numeric($amount) || $amount <= 0) $errors['amount'] = 'Valid amount is required.';
        if (empty($phone) || !preg_match('/^\d{10}$/', $phone)) $errors['phone'] = 'Valid phone number is required.';

        if (empty($errors) && isset($_SESSION['user_id'])) {
            try {
                // Simulate payment success (replace with M-Pesa API call in production)
                $stmt = $pdo->prepare("INSERT INTO payments (user_id, event_name, amount, phone_number, payment_status) VALUES (?, ?, ?, ?, 'success')");
                $stmt->execute([$_SESSION['user_id'], $event, $amount, $phone]);
                $success_message = "Payment processed successfully for $amount Ksh!";
            } catch (PDOException $e) {
                $errors['payment'] = 'Payment processing failed: ' . $e->getMessage();
            }
        }
    } elseif (isset($_POST['reserve_seat'])) { // Handle seat reservation
        $event = trim($_POST['event']);
        $name = trim($_POST['name']);
        $seat_number = trim($_POST['seat_number']);

        // Validation
        if (empty($event)) $errors['event'] = 'Please select an event.';
        if (empty($name)) $errors['name'] = 'Name is required.';
        if (empty($seat_number) || !is_numeric($seat_number) || $seat_number < 1 || $seat_number > 100) $errors['seat_number'] = 'Seat number must be between 1 and 100.';

        if (!$payment_confirmed) {
            $errors['payment'] = 'Payment must be completed before reserving a seat.';
        }

        if (empty($errors) && isset($_SESSION['user_id'])) {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM seat_reservations WHERE event_name = ? AND seat_number = ?");
                $stmt->execute([$event, $seat_number]);
                if ($stmt->fetchColumn()) {
                    $errors['seat_number'] = 'This seat is already taken for the selected event.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO seat_reservations (event_name, seat_number, user_name) VALUES (?, ?, ?)");
                    $stmt->execute([$event, $seat_number, $name]);
                    $success_message = "Seat $seat_number for $event reserved successfully by $name!";
                }
            } catch (PDOException $e) {
                $errors['database'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch top 3 winners
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
        /* [Existing styles remain unchanged, omitted for brevity] */
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

        button:disabled {
            background-color: #6b7280;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- [Sidebar menu remains unchanged, omitted for brevity] -->
    </div>
    <div class="main-content">
        <div class="container">
            <h1>Dashboard</h1>
            <div class="disclaimer">
                <p>Disclaimer: Some functions are still being developed. Stay tuned for updates.</p>
            </div>
            <div class="card-container dashboard-grid">
                <!-- [Winner and explore cards remain unchanged, omitted for brevity] -->
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
                            <select name="event" id="event" required>
                                <option value="">-- Select an Event --</option>
                                <option value="Genz Fashion Show">Genz Fashion Show</option>
                                <option value="WestSide Talent Show">WestSide Talent Show</option>
                                <option value="Tech Explorer">Tech Explorer</option>
                            </select>
                            <?php if (isset($errors['event'])): ?><p class="error"><?php echo $errors['event']; ?></p><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="name">Your Name:</label>
                            <input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            <?php if (isset($errors['name'])): ?><p class="error"><?php echo $errors['name']; ?></p><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="seat_number">Seat Number (1-100):</label>
                            <input type="number" name="seat_number" id="seat_number" min="1" max="100" value="<?php echo isset($_POST['seat_number']) ? htmlspecialchars($_POST['seat_number']) : ''; ?>" required>
                            <?php if (isset($errors['seat_number'])): ?><p class="error"><?php echo $errors['seat_number']; ?></p><?php endif; ?>
                        </div>
                        <div class="payment-card">
                            <div class="d-flex flex-row justify-content-around">
                                <div class="mpesa active"><span>Mpesa</span></div>
                            </div>
                            <div class="media mt-3 pl-2">
                                <div class="media-body">
                                    <h6 class="mt-1">Enter Amount & Number</h6>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="text" class="form-control" name="amount" value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>" required>
                                        <?php if (isset($errors['amount'])): ?><p class="error"><?php echo $errors['amount']; ?></p><?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                                        <?php if (isset($errors['phone'])): ?><p class="error"><?php echo $errors['phone']; ?></p><?php endif; ?>
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

                const event = $('#event').val();
                const name = $('#name').val().trim();
                const seatNumber = $('#seat_number').val().trim();
                const amount = $('input[name="amount"]').val().trim();
                const phone = $('input[name="phone"]').val().trim();

                if (!event) {
                    $('#event').next('.error').text('Please select an event.').show();
                    hasError = true;
                }
                if (!name) {
                    $('#name').next('.error').text('Name is required.').show();
                    hasError = true;
                }
                if (!seatNumber || seatNumber < 1 || seatNumber > 100) {
                    $('#seat_number').next('.error').text('Seat number must be between 1 and 100.').show();
                    hasError = true;
                }
                if (!amount || isNaN(amount) || amount <= 0) {
                    $('input[name="amount"]').next('.error').text('Valid amount is required.').show();
                    hasError = true;
                }
                if (!phone || !/^\d{10}$/.test(phone)) {
                    $('input[name="phone"]').next('.error').text('Valid phone number is required.').show();
                    hasError = true;
                }

                if (hasError) e.preventDefault();

                $(this).find('#event, #name, #seat_number, input[name="amount"], input[name="phone"]').on('input change', function() {
                    $(this).next('.error').hide();
                });
            });
        });
    </script>
</body>
</html>