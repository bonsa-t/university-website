<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$college_id = $_SESSION['college_id'];

// Get selected filters
$selected_year = isset($_GET['year']) ? $_GET['year'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';

// Base query
$query = "SELECT * FROM grades WHERE college_id = ?";
$params = [$college_id];

// Add filters
if ($selected_year) {
    $query .= " AND year = ?";
    $params[] = $selected_year;
}
if ($selected_semester) {
    $query .= " AND semester = ?";
    $params[] = $selected_semester;
}

$query .= " ORDER BY year, semester";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate GPA and CGPA
function calculateGPA($grades) {
    $gradePoints = [
        'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
        'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
        'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
        'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
    ];
    
    $totalPoints = 0;
    $totalSubjects = count($grades);
    
    foreach ($grades as $grade) {
        $totalPoints += $gradePoints[$grade['grade']] ?? 0;
    }
    
    return $totalSubjects > 0 ? round($totalPoints / $totalSubjects, 2) : 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
  
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .filters {
            margin: 20px 0;
            padding: 15px;
            background:rgb(56, 78, 105);
            border-radius: 5px;
            color:white;
        }
        .gpa-box {
            background: #e8f5e9;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
        }
        .gpa-item {
            text-align: center;
            
        }
        .gpa-value {
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32;
        }
        
    .message {
        background: rgb(56, 78, 105);
        padding: 20px;
        text-align: center;
        border-radius: 5px;
        margin: 20px 0;
    }
</style>

    <!-- Navigation -->
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-university"></i>
            <span>SV College</span>
        </div>
        <ul class="nav-links">
            <li><a href="index.php#home">Home</a></li>
            <li><a href="index.php#about">About Us</a></li>
            <li class="dropdown">
                <a href="index.php#academics">Academics ▾</a>
                <div class="dropdown-content">
                    <a href="index.php#departments">Departments</a>
                    <a href="index.php#faculty">Faculty</a>
                    <a href="index.php#admission">Admission</a>
                    <a href="student_dashboard.php">View Grade</a>
            <a href="admin_dashboard.php">A.Dashboard</a>
            
                </div>
            </li>
            <li><a href="index.php#news">News & Events</a></li>
            <li><a href="index.php#contact">Contact</a></li>
           
        </ul>
        <button class="theme-toggle">
            🌓
        </button>
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>
</head>
<body>
    <div class="container">
        <h2>My Academic Record</h2>
      
    <h2>My Academic Record</h2>
        <div class="filters">
            <form method="GET" action="">
                <select name="year">
                    <option value="">Select Year</option>
                    <?php for($year = 2014; $year <= 2050; $year++): ?>
                        <option value="<?php echo $year; ?>" <?php echo $selected_year == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <select name="semester">
                    <option value="">Select Semester</option>
                    <?php 
                    $semesters = [
                        1 => 'SEM I',
                        2 => 'SEM II', 
                        3 => 'SEM III'
                    ];
                    foreach($semesters as $value => $label): 
                    ?>
                        <option value="<?php echo $value; ?>" <?php echo $selected_semester == $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>
   
        <div class="gpa-box">
            <div class="gpa-item">
                <div class="gpa-label">Current GPA</div>
                <div class="gpa-value"><?php echo calculateGPA($grades); ?></div>
            </div>
            <div class="gpa-item">
                <div class="gpa-label">CGPA</div>
                <div class="gpa-value">
                    <?php 
                    $stmt = $pdo->prepare("SELECT * FROM grades WHERE college_id = ?");
                    $stmt->execute([$college_id]);
                    $allGrades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo calculateGPA($allGrades);
                    ?>
                </div>
            </div>
        </div>

        <table>
            <tr>
                <th>Year</th>
                <th>Semester</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>Date</th>
            </tr>
            <?php foreach ($grades as $grade): ?>
            <tr>
                <td><?php echo $grade['year']; ?></td>
                <td><?php echo $grade['semester']; ?></td>
                <td><?php echo $grade['subject']; ?></td>
                <td><?php echo $grade['grade']; ?></td>
                <td><?php echo $grade['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <a href="logout.php"><button>Logout</button></a>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
   
    <script src="script.js"></script>
</body>
</html>
