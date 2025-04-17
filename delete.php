<?php
include 'db.php';

$cef = $_GET['cef'] ?? '';

if ($cef) {
    $stmt = $conn->prepare("DELETE FROM etudiants WHERE cef = ?");
    $stmt->execute([$cef]);
}

header("Location: index.php");
exit;
