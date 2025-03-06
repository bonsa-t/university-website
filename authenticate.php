<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $college_id = $_POST['college_id'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE college_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$college_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['college_id'] = $user['college_id'];
        
        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: student_dashboard.php");
            exit();
        }
    }
    
    header("Location: login.php?error=1");
    exit();
}
?>
