<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
    $college_id = $_POST['college_id'];
    $year = $_POST['year'];
    $semester = $_POST['semester']; // This will now correctly capture SEM I, SEM II, or SEM III
    $subject = $_POST['subject'];
    $grade = $_POST['grade'];
    
    $stmt = $pdo->prepare("INSERT INTO grades (college_id, year, semester, subject, grade) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$college_id, $year, $semester, $subject, $grade])) {
        header("Location: admin_dashboard.php?success=1");
    } else {
        header("Location: admin_dashboard.php?error=1");
    }
    exit();
}
?>
