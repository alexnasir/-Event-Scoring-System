<?php
session_start();
include 'db.php';

$errors = [];
$success_message = '';

if (!isset($_SESSION['judge_id'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $judge_id = trim($_POST['judge_id']);
        if (empty($judge_id)) {
            $errors['judge_id'] = 'Judge ID is required.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT display_name FROM judges WHERE id = ?");
                $stmt->execute([$judge_id]);
                if ($row = $stmt->fetch()) {
                    $_SESSION['judge_id'] = $judge_id;
                    $_SESSION['judge_name'] = $row['display_name'];
                    header("Location: judge.php");
                    exit;
                } else {
                    $errors['judge_id'] = 'Invalid Judge ID.';
                }
            } catch (PDOException $e) {
                $errors['general'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Judge Login</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body class="form-page">
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
        <div class="container form-container">
            <h1>Judge Login</h1>
            <form method="post" id="login-form" class="modern-form">
                <div class="form-group">
                    <label for="judge_id">Enter your Judge ID:</label>
                    <input type="text" name="judge_id" id="judge_id" value="<?php echo isset($_POST['judge_id']) ? htmlspecialchars($_POST['judge_id']) : ''; ?>">
                    <p class="error" style="display: none;"></p>
                    <?php if (isset($errors['judge_id'])): ?>
                        <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['judge_id']; ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="gradient-btn">Proceed</button>
                <?php if (isset($errors['general'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                $('.error').hide();
                const judgeId = $('#judge_id').val().trim();
                if (!judgeId) {
                    $('#judge_id').next('.error').text('Judge ID is required.').show();
                    e.preventDefault();
                }
                $('#judge_id').on('input', function() {
                    $(this).next('.error').hide();
                });
            });

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
    <?php
} else {
    $judge_id = $_SESSION['judge_id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        $user_name = $user ? htmlspecialchars($user['name']) : 'Unknown User';

        if (isset($_POST['action']) && $_POST['action'] == 'delete') {
            try {
                // Fetch the score before deleting
                $stmt = $pdo->prepare("SELECT points FROM scores WHERE judge_id = ? AND user_id = ?");
                $stmt->execute([$judge_id, $user_id]);
                $score = $stmt->fetch();
                
                if ($score) {
                    // Log the delete action into score_history table
                    $stmt = $pdo->prepare("INSERT INTO score_history (judge_id, user_id, action, points) VALUES (?, ?, 'Delete', ?)");
                    $stmt->execute([$judge_id, $user_id, $score['points']]);
                    
                    // Now delete the score
                    $stmt = $pdo->prepare("DELETE FROM scores WHERE judge_id = ? AND user_id = ?");
                    $stmt->execute([$judge_id, $user_id]);
                    
                    $success_message = 'Score for ' . $user_name . ' deleted successfully.';
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => true, 'message' => $success_message]);
                        exit;
                    }
                } else {
                    $errors[$user_id] = 'No score found to delete for ' . $user_name . '.';
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => false, 'message' => $errors[$user_id]]);
                        exit;
                    }
                }
            } catch (PDOException $e) {
                $errors[$user_id] = 'Database error: ' . $e->getMessage();
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['success' => false, 'message' => $errors[$user_id]]);
                    exit;
                }
            }
        } else {
            $points = trim($_POST['points']);
            if (empty($points)) {
                $errors[$user_id] = 'Points for ' . $user_name . ' are required.';
            } elseif (!is_numeric($points) || $points < 1 || $points > 100) {
                $errors[$user_id] = 'Points for ' . $user_name . ' must be between 1 and 100.';
            } else {
                try {
                    // Check if this is an update (existing score)
                    $stmt = $pdo->prepare("SELECT points FROM scores WHERE judge_id = ? AND user_id = ?");
                    $stmt->execute([$judge_id, $user_id]);
                    $existing_score = $stmt->fetch();
                    
                    // Insert or update the score
                    $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, points) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE points = ?");
                    $stmt->execute([$judge_id, $user_id, $points, $points]);
                    
                    // Log the save action into score_history table
                    $stmt = $pdo->prepare("INSERT INTO score_history (judge_id, user_id, action, points) VALUES (?, ?, 'Save', ?)");
                    $stmt->execute([$judge_id, $user_id, $points]);
                    
                    $success_message = 'Score for ' . $user_name . ' saved successfully.';
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => true, 'message' => $success_message]);
                        exit;
                    }
                } catch (PDOException $e) {
                    $errors[$user_id] = 'Database error: ' . $e->getMessage();
                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        echo json_encode(['success' => false, 'message' => $errors[$user_id]]);
                        exit;
                    }
                }
            }
        }
    }
    try {
        $stmt = $pdo->query("SELECT id, name FROM users");
        $users = $stmt->fetchAll();
    } catch (PDOException $e) {
        $errors['general'] = 'Database error: ' . $e->getMessage();
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Score Users</title>
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
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-label">Home</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="admin.php">
                    <i class="fas fa-user-shield"></i>
                    <span class="sidebar-label">Admin Panel</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="judge.php" class="active">
                    <i class="fas fa-gavel"></i>
                    <span class="sidebar-label">Judge Portal</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="scoreboard.php">
                    <i class="fas fa-trophy"></i>
                    <span class="sidebar-label">Scoreboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="history.php">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-label">History</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-label">Logout</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="main-content">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['judge_name']); ?></h1>
            <?php if ($success_message): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
            <?php endif; ?>
            <div class="card-container">
                <?php foreach ($users as $user): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT points FROM scores WHERE judge_id = ? AND user_id = ?");
                    $stmt->execute([$judge_id, $user['id']]);
                    $score = $stmt->fetch();
                    $points = $score ? $score['points'] : '';
                    ?>
                    <div class="card" data-user-id="<?php echo $user['id']; ?>">
                        <form method="post" class="score-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                            <div class="form-group">
                                <label for="points_<?php echo $user['id']; ?>">Give Score:</label>
                                <input type="number" id="points_<?php echo $user['id']; ?>" name="points" value="<?php echo $points; ?>" min="1" max="100">
                                <p class="error" style="display: none;"></p>
                                <?php if (isset($errors[$user['id']])): ?>
                                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors[$user['id']]; ?></p>
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="save-btn">Save</button>
                        </form>
                        <?php if ($score): ?>
                            <form method="post" class="delete-form">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                            <div class="delete-confirmation" style="display: none;">
                                <div class="confirmation-content">
                                    <p>Are you sure you want to delete the score for <?php echo htmlspecialchars($user['name']); ?>?</p>
                                    <div class="confirmation-buttons">
                                        <button class="confirm-delete">Confirm</button>
                                        <button class="cancel-delete">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.score-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const card = form.closest('.card');
                const errorElement = form.find('.error');
                errorElement.hide();

                const points = form.find('input[name="points"]').val().trim();
                if (!points) {
                    errorElement.text('Points are required.').show();
                    return;
                }
                if (!isNumeric(points) || points < 1 || points > 100) {
                    errorElement.text('Points must be between 1 and 100.').show();
                    return;
                }

                $.ajax({
                    url: 'judge.php',
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const message = $('<p class="message"><i class="fas fa-check-circle"></i> ' + response.message + '</p>');
                            $('.container').prepend(message);
                            setMessageTimeout();
                            // Add delete button if this is a new score
                            if (!card.find('.delete-form').length) {
                                const deleteForm = $('<form method="post" class="delete-form">' +
                                    '<input type="hidden" name="user_id" value="' + form.find('input[name="user_id"]').val() + '">' +
                                    '<input type="hidden" name="action" value="delete">' +
                                    '<button type="submit" class="delete-btn">Delete</button>' +
                                    '</form>' +
                                    '<div class="delete-confirmation" style="display: none;">' +
                                    '<div class="confirmation-content">' +
                                    '<p>Are you sure you want to delete the score for ' + card.find('p strong').text().replace('Name: ', '') + '?</p>' +
                                    '<div class="confirmation-buttons">' +
                                    '<button class="confirm-delete">Confirm</button>' +
                                    '<button class="cancel-delete">Cancel</button>' +
                                    '</div></div></div>');
                                form.after(deleteForm);
                            }
                        } else {
                            errorElement.text(response.message).show();
                        }
                    },
                    error: function(xhr, status, error) {
                        errorElement.text('Error saving score: ' + error).show();
                    }
                });

                form.find('input[name="points"]').on('input', function() {
                    errorElement.hide();
                });
            });

            function isNumeric(value) {
                return !isNaN(parseFloat(value)) && isFinite(value);
            }

            let formToSubmit = null;

            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                formToSubmit = $(this);
                const card = $(this).closest('.card');
                card.find('.delete-confirmation').fadeIn();
            });

            $(document).on('click', '.confirm-delete', function() {
                const card = $(this).closest('.card');
                const form = card.find('.delete-form');
                const scoreInput = card.find('input[name="points"]');
                const deleteFormContainer = card.find('.delete-form');
                const confirmation = card.find('.delete-confirmation');

                $.ajax({
                    url: 'judge.php',
                    method: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Clear the score input
                            scoreInput.val('');
                            // Remove the delete button
                            deleteFormContainer.remove();
                            // Show success message
                            const message = $('<p class="message"><i class="fas fa-check-circle"></i> ' + response.message + '</p>');
                            $('.container').prepend(message);
                            setMessageTimeout();
                        } else {
                            const errorElement = card.find('.score-form .error');
                            errorElement.text(response.message).show();
                        }
                    },
                    error: function(xhr, status, error) {
                        const errorElement = card.find('.score-form .error');
                        errorElement.text('Error deleting score: ' + error).show();
                    }
                });

                confirmation.fadeOut();
            });

            $(document).on('click', '.cancel-delete', function() {
                const card = $(this).closest('.card');
                formToSubmit = null;
                card.find('.delete-confirmation').fadeOut();
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
            $('.score-form, .delete-form').on('submit', function() {
                setTimeout(setMessageTimeout, 100);
            });

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
    <style>
        .delete-btn {
            padding: 0.3rem 0.6rem;
            background-color: #dc2626;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            margin-top: 0.5rem;
        }

        .delete-btn:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
        }

        .delete-btn:active {
            transform: translateY(0);
        }

        .delete-confirmation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .confirmation-content {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .confirmation-buttons {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-delete, .cancel-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .confirm-delete {
            background-color: #dc2626;
            color: #fff;
        }

        .confirm-delete:hover {
            background-color: #b91c1c;
        }

        .cancel-delete {
            background-color: #6b7280;
            color: #fff;
        }

        .cancel-delete:hover {
            background-color: #4b5563;
        }
    </style>
    </body>
    </html>
    <?php
}
?>