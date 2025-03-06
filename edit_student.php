<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['role'] == 'admin') {
    $college_id = $_POST['college_id'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $batch = $_POST['batch'];
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("UPDATE users SET name = ?, department = ?, batch = ?, email = ? WHERE college_id = ?");
    
    if ($stmt->execute([$name, $department, $batch, $email, $college_id])) {
        header("Location: admin_dashboard.php?success=student_updated");
    } else {
        header("Location: admin_dashboard.php?error=update_failed");
    }
    exit();
}

// Get student data for edit form
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE college_id = ? AND role = 'student'");
    $stmt->execute([$_GET['id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Edit Student</h2>
        <div class="grade-form">
            <form action="edit_student.php" method="POST">
                <input type="hidden" name="college_id" value="<?php echo $student['college_id']; ?>">
                <input type="text" name="name" value="<?php echo $student['name']; ?>" required>
                <select name="department" required>
                    <option value="CSE" <?php echo $student['department'] == 'CSE' ? 'selected' : ''; ?>>Computer Science</option>
                    <option value="ECE" <?php echo $student['department'] == 'ECE' ? 'selected' : ''; ?>>Electronics</option>
                    <option value="ME" <?php echo $student['department'] == 'ME' ? 'selected' : ''; ?>>Mechanical</option>
                    <option value="CE" <?php echo $student['department'] == 'CE' ? 'selected' : ''; ?>>Civil</option>
                </select>
                <select name="batch" required>
                    <?php for($year = 2020; $year <= date('Y'); $year++): ?>
                        <option value="<?php echo $year; ?>" <?php echo $student['batch'] == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <input type="email" name="email" value="<?php echo $student['email']; ?>" required>
                <button type="submit">Update Student</button>
            </form>
        </div>
    </div>
</body>
</html>
