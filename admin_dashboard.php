<?php
session_start();
require_once 'config/database.php';

// Redirect if not logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle news post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['news_form'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $content = $_POST['content'];
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/news/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }
    
    $stmt = $pdo->prepare("INSERT INTO news (title, description, date, content, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $date, $content, $image_path]);
    header("Location: admin_dashboard.php");
    exit();
}

// Handle news deletion
if (isset($_GET['delete_news'])) {
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$_GET['delete_news']]);
    header("Location: admin_dashboard.php");
    exit();
}

// Handle news edit form display
$edit_news = null;
if (isset($_GET['edit_news'])) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$_GET['edit_news']]);
    $edit_news = $stmt->fetch();
}

// Handle news edit form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_news_form'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $content = $_POST['content'];
    
    $stmt = $pdo->prepare("UPDATE news SET title = ?, description = ?, date = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $description, $date, $content, $id]);
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch existing news
$news = $pdo->query("SELECT * FROM news ORDER BY date DESC")->fetchAll();

// Fetch grades with filters
$selected_year = isset($_GET['year']) ? $_GET['year'] : '';
$selected_semester = isset($_GET['semester']) ? $_GET['semester'] : '';

if (isset($_GET['college_id']) || isset($_GET['year']) || isset($_GET['semester'])) {
    $query = "SELECT g.*, u.name FROM grades g JOIN users u ON g.college_id = u.college_id WHERE 1=1";
    $params = [];

    if (!empty($_GET['college_id'])) {
        $query .= " AND g.college_id = ?";
        $params[] = $_GET['college_id'];
    }
    
    if ($selected_year) {
        $query .= " AND g.year = ?";
        $params[] = $selected_year;
    }
    
    if ($selected_semester) {
        $query .= " AND g.semester = ?";
        $params[] = $selected_semester;
    }

    $query .= " ORDER BY g.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $grades = [];
}

// Fetch students
$students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY department, batch, name")->fetchAll();

// Fetch messages
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard - SV College</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_dashboard.css">
    <style>
        .filters {
            margin: 20px 0;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .grade-form {
            background: #e8f5e9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .notification {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            color: white;
        }
        .notification.success {
            background: #4CAF50;
        }
        .notification.error {
            background: #f44336;
        }
        .edit-news-form {
            background: #f9f9f9;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .messages-table th, .messages-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .messages-table th {
            background: #214780;
            color: white;
        }
        .messages-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .messages-table tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="admin-nav">
            <ul>
                <li><a href="#" class="nav-link active" data-section="add-grades">Add Grades</a></li>
                <li><a href="#" class="nav-link" data-section="view-grades">View Grades</a></li>
                <li><a href="#" class="nav-link" data-section="add-student">Add Student</a></li>
                <li><a href="#" class="nav-link" data-section="student-list">Student List</a></li>
                <li><a href="#" class="nav-link" data-section="news-management">News Management</a></li>
                <li><a href="#" class="nav-link" data-section="messages">Messages</a></li>
            </ul>
        </nav>

        <!-- Add Grades Section -->
        <div id="add-grades" class="section active">
            <h2>Add Grades</h2>
            <div class="grade-form">
                <form action="add_grade.php" method="POST">
                    <input type="text" name="college_id" placeholder="Student College ID" required>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        <?php for($year = 2014; $year <= 2050; $year++): ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="1">SEM I</option>
                        <option value="2">SEM II</option>
                        <option value="3">SEM III</option>
                    </select>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <input type="text" name="grade" placeholder="Grade" required>
                    <button type="submit">Add Grade</button>
                </form>
            </div>
        </div>

        <!-- View Grades Section -->
        <div id="view-grades" class="section">
            <h2>View All Grades</h2>
            <div class="filters">
                <form method="GET" action="">
                    <input type="text" name="college_id" placeholder="Filter by Student College ID" 
                           value="<?php echo isset($_GET['college_id']) ? $_GET['college_id'] : ''; ?>">
                    <select name="year">
                        <option value="">Filter by Year</option>
                        <?php for($year = 2014; $year <= 2050; $year++): ?>
                            <option value="<?php echo $year; ?>" <?php echo $selected_year == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <select name="semester">
                        <option value="">Filter by Semester</option>
                        <option value="1">SEM I</option>
                        <option value="2">SEM II</option>
                        <option value="3">SEM III</option>
                    </select>
                    <button type="submit" class="filter-btn">Filter</button>
                </form>
            </div>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>College ID</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($grades as $row): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['college_id']; ?></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td><?php echo $row['subject']; ?></td>
                        <td><?php echo $row['grade']; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                        <td>
                            <form action="delete_grade.php" method="POST" style="display: inline;">
                                <input type="hidden" name="grade_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Add Student Section -->
        <div id="add-student" class="section">
            <h2>Add New Student</h2>
            <div class="grade-form">
                <form action="register_student.php" method="POST">
                    <input type="text" name="college_id" placeholder="Student College ID" required>
                    <input type="text" name="name" placeholder="Student Name" required>
                    <select name="department" required>
                        <option value="">Select Department</option>
                        <option value="CSE">Computer Science</option>
                        <option value="ECE">Electronics</option>
                        <option value="ME">Mechanical</option>
                        <option value="CE">Civil</option>
                    </select>
                    <select name="batch" required>
                        <option value="">Select Batch Year</option>
                        <?php for($year = 2020; $year <= date('Y'); $year++): ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                    <input type="email" name="email" placeholder="Student Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Add Student</button>
                </form>
            </div>
        </div>

        <!-- Student List Section -->
        <div id="student-list" class="section">
            <h2>Student List</h2>
            <div class="search-box">
                <form onsubmit="event.preventDefault(); searchStudents();">
                    <input type="text" id="studentSearch" placeholder="Search by College ID or Name...">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
            <table id="studentTable">
                <thead>
                    <tr>
                        <th>College ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Batch</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['college_id']; ?></td>
                            <td><?php echo $student['name']; ?></td>
                            <td><?php echo $student['department']; ?></td>
                            <td><?php echo $student['batch']; ?></td>
                            <td><?php echo $student['email']; ?></td>
                            <td>
                                <button class="edit-btn" onclick="window.location.href='edit_student.php?id=<?php echo $student['college_id']; ?>'">Edit</button>
                                <form action="delete_student.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="college_id" value="<?php echo $student['college_id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- News Management Section -->
        <div id="news-management" class="section">
            <h2>News Management</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="news_form">
                <input type="text" name="title" placeholder="News Title" required>
                <textarea name="description" placeholder="Short Description" required></textarea>
                <input type="date" name="date" required>
                <textarea name="content" placeholder="Full Content" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Add News</button>
            </form>

            <?php if (isset($edit_news)): ?>
                <div class="edit-news-form">
                    <h2>Edit News</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit_news_form">
                        <input type="hidden" name="id" value="<?php echo $edit_news['id']; ?>">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($edit_news['title']); ?>" placeholder="News Title" required>
                        <textarea name="description" placeholder="Short Description" required><?php echo htmlspecialchars($edit_news['description']); ?></textarea>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($edit_news['date']); ?>" required>
                        <textarea name="content" placeholder="Full Content" required><?php echo htmlspecialchars($edit_news['content']); ?></textarea>
                        <input type="file" name="image" accept="image/*">
                        <button type="submit">Update News</button>
                    </form>
                </div>
            <?php endif; ?>

            <h2>Existing News</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td><?php echo date('Y-m-d', strtotime($item['date'])); ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>
                                <a href="admin_dashboard.php?edit_news=<?php echo $item['id']; ?>" class="edit-btn">Edit</a>
                                <a href="admin_dashboard.php?delete_news=<?php echo $item['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Messages Section -->
        <div id="messages" class="section">
            <h2>Messages</h2>
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($message['name']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['message']); ?></td>
                            <td><?php echo htmlspecialchars($message['status']); ?></td>
                            <td><?php echo date('F d, Y H:i', strtotime($message['created_at'])); ?></td>
                            <td>
                                <a href="mailto:<?php echo $message['email']; ?>?subject=Re: Your Message to Silicon Valley College&body=Dear <?php echo htmlspecialchars($message['name']); ?>," class="reply-btn">Reply</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Notifications -->
        <?php if (isset($_GET['success'])): ?>
            <div class="notification success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="notification error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Logout Button -->
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <script>
        // JavaScript for section switching
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
                    document.getElementById(this.dataset.section).classList.add('active');
                });
            });
        });

        // JavaScript for student search
        function searchStudents() {
            const input = document.getElementById('studentSearch').value.toUpperCase();
            const table = document.getElementById('studentTable');
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const cols = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cols.length; j++) {
                    if (cols[j].textContent.toUpperCase().indexOf(input) > -1) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>
</body>
</html>