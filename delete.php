<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=kic_practice;charset=utf8', 'root', '');
$stmt = $pdo->prepare("DELETE FROM requests WHERE id = ?");
$stmt->execute([$_GET['id']]);
header('Location: index.php');
?>