<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];

// Vérification que l'équipement existe
$stmt = $pdo->prepare("SELECT id FROM equipement WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}

// Suppression
$stmt = $pdo->prepare("DELETE FROM equipement WHERE id = ?");
$stmt->execute([$id]);

header('Location: dashboard.php');
exit;
?>