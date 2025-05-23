<?php
session_start();
session_destroy();
header("Location: judge.php");
exit;
?>