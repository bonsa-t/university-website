<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
    $grade_id = $_POST['grade_id'];
    
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->execute([$grade_id]);
    
    header("Location: admin_dashboard.php?success=deleted");
    exit();
}
?>
