<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
    $college_id = $_POST['college_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (college_id, name, department, batch, email, password, role) VALUES (?, ?, ?, ?, ?, ?, 'student')");
    
    if ($stmt->execute([$college_id, $name, $_POST['department'], $_POST['batch'], $email, $password])) {
        header("Location: admin_dashboard.php?success=student_added");
    } else {
        header("Location: admin_dashboard.php?error=registration_failed");
    }
    exit();
}
?>
