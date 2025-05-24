<?php
session_start();
include 'db.php';

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) $errors['email'] = "Email is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, name, password FROM accounts WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $success_message = "{$user['name']}, Login successful.";
                $_SESSION['login_message'] = $success_message;
                header("Location: index.php");
                exit;
            } else {
                $errors['general'] = "Invalid email or password.";
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
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post" id="login-form">
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
            <button type="submit">Login</button>
            <?php if (isset($_SESSION['signup_message'])): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $_SESSION['signup_message']; unset($_SESSION['signup_message']); ?></p>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <p class="message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($errors['general'])): ?>
                <p class="error"><i class="fas fa-exclamation-circle"></i> <?php echo $errors['general']; ?></p>
            <?php endif; ?>
        </form>
        <a href="signup.php" class="logout">Don't have an account? Sign Up</a>
    </div>
    <script>
        $(document).ready(function() {
            $('#login-form').on('submit', function(e) {
                $('.error').hide();
                let hasError = false;

                const email = $('#email').val().trim();
                const password = $('#password').val().trim();

                if (!email) {
                    $('#email').next('.error').text('Email is required.').show();
                    hasError = true;
                }
                if (!password) {
                    $('#password').next('.error').text('Password is required.').show();
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }

                $('#email, #password').on('input', function() {
                    $(this).next('.error').hide();
                });
            });
        });
    </script>
</body>
</html>