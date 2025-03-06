<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
    $college_id = $_POST['college_id'];
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE college_id = ? AND role = 'student'");
    
    if ($stmt->execute([$college_id])) {
        header("Location: admin_dashboard.php?success=student_deleted");
    } else {
        header("Location: admin_dashboard.php?error=delete_failed");
    }
    exit();
}
