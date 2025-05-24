<?php
$dsn = 'mysql:host=localhost;dbname=event_scoring';
$username = 'root';
$password = 'newpassword';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
