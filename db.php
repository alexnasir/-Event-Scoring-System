<?php
$dsn = 'mysql:host=sql311.infinityfree.com;dbname=if0_39063872_event_scoring';
$username = 'if0_39063872';
$password = 'xuOm1DTzjKIyO';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
