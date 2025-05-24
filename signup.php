<?php
session_start();
include 'db.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name)) $errors['name'] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
    if (strlen($password) < 6) $errors['password'] = "Password must be at least 6 characters.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM accounts WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $errors['email'] = "Email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO accounts (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password]);
                $success_message = "$name, Your Account created successfully.";
                $_SESSION['signup_message'] = $success_message;
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            $errors['general'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Sign Up</h1>
        <form method="post" id="signup-form">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <p class="error" style="display: none;"></p>
                <?php if (isset($errors['name'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['name']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <p class="error" style="display: none;"></p>
                <?php if (isset($errors['email'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password">
                <p class="error" style="display: none;"></p>
                <?php if (isset($errors['password'])): ?>
                    <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['password']; ?></p>
                <?php endif; ?>
            </div>
            <button type="submit">Sign Up</button>
            <?php if ($success_message): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
            <?php endif; ?>
        </form>
        <a href="login.php" class="logout">Already have an account? Login</a>
        <a href="index.php" class="logout">Not to SignUp now</a>

    </div>
    <script>
        $(document).ready(function() {
            $('#signup-form').on('submit', function(e) {
                $('.error').hide();
                let hasError = false;

                const name = $('#name').val().trim();
                const email = $('#email').val().trim();
                const password = $('#password').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!name) {
                    $('#name').next('.error').text('Name is required.').show();
                    hasError = true;
                }
                if (!email) {
                    $('#email').next('.error').text('Email is required.').show();
                    hasError = true;
                } else if (!emailRegex.test(email)) {
                    $('#email').next('.error').text('Invalid email format.').show();
                    hasError = true;
                }
                if (!password) {
                    $('#password').next('.error').text('Password is required.').show();
                    hasError = true;
                } else if (password.length < 6) {
                    $('#password').next('.error').text('Password must be at least 6 characters.').show();
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }

                $('#name, #email, #password').on('input', function() {
                    $(this).next('.error').hide();
                });
            });
        });
    </script>
</body>
</html>