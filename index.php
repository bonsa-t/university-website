<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silicon Valley College</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* Success and Error Messages */
        .success-message {
            padding: 10px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            padding: 10px;
            background: #f44336;
            color: white;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-university"></i>
            <span>SV College</span>
        </div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About Us</a></li>
            <li class="dropdown">
                <a href="#academics">Academics ▾</a>
                <div class="dropdown-content">
                    <a href="#faculty">Faculty</a>
                    <a href="#admission">Admission</a>
                    <a href="student_dashboard.php">View Grade</a>
                    <a href="admin_dashboard.php">A.Dashboard</a>
                </div>
            </li>
            <li><a href="#news">News & Events</a></li>
            <li><a href="#contact">Contact</a></li>
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

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content" data-aos="fade-up">
            <h1>Welcome to Silicon Valley College</h1>
            <p>Shaping Tomorrow's Technology Leaders</p>
            <button class="cta-button">Apply Now</button>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="courses">
        <h2 data-aos="fade-up">Our Programs</h2>
        <div class="course-grid">
            <div class="course-card" data-aos="fade-up">
                <i class="fas fa-code"></i>
                <h3>Computer Science</h3>
                <p>Learn cutting-edge programming and software development</p>
            </div>
            <div class="course-card" data-aos="fade-up">
                <i class="fas fa-robot"></i>
                <h3>Artificial Intelligence</h3>
                <p>Explore machine learning and AI applications</p>
            </div>
            <div class="course-card" data-aos="fade-up">
                <i class="fas fa-network-wired"></i>
                <h3>Data Science</h3>
                <p>Master big data analytics and visualization</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <h2>About Us</h2>
        <div class="container">
            <div class="mission-vision" data-aos="fade-up">
                <div class="mv-card">
                    <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                    <p>To cultivate innovative leaders through excellence in education, research, and industry collaboration.</p>
                </div>
                <div class="mv-card">
                    <h3><i class="fas fa-eye"></i> Our Vision</h3>
                    <p>To be a globally recognized institution that transforms technology education.</p>
                </div>
                <div class="mv-card">
                    <h3><i class="fas fa-star"></i> Our Values</h3>
                    <ul>
                        <li>Innovation</li>
                        <li>Excellence</li>
                        <li>Integrity</li>
                        <li>Collaboration</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Faculty Section -->
    <section id="faculty" class="faculty-section">
        <h2>Our Leadership</h2>
        <div class="department-heads">
            <!-- Department Head Cards -->
            <div class="head-card" data-aos="fade-up">
                <img src="uploads/news/1741121356_IMG_3130.JPG" alt="Department Head">
                <h3>Dr. Sarah Johnson</h3>
                <p>Head of Computer Science</p>
                <blockquote>"Embracing technology to shape tomorrow's innovators."</blockquote>
            </div>
            <div class="head-card" data-aos="fade-up">
                <img src="uploads/news/1741121356_IMG_3130.JPG" alt="Department Head">
                <h3>Dr. Sarah Johnson</h3>
                <p>Head of Computer Science</p>
                <blockquote>"Embracing technology to shape tomorrow's innovators."</blockquote>
            </div>
            <div class="head-card" data-aos="fade-up">
                <img src="uploads/news/1741121356_IMG_3130.JPG" alt="Department Head">
                <h3>Dr. Sarah Johnson</h3>
                <p>Head of Computer Science</p>
                <blockquote>"Embracing technology to shape tomorrow's innovators."</blockquote>
            </div>
            <div class="head-card" data-aos="fade-up">
                <img src="uploads/news/1741121356_IMG_3130.JPG" alt="Department Head">
                <h3>Dr. Sarah Johnson</h3>
                <p>Head of Computer Science</p>
                <blockquote>"Embracing technology to shape tomorrow's innovators."</blockquote>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="news-section">
        <h2>Latest Updates</h2>
        <div class="news-grid">
            <?php
            require_once 'config/database.php';
            
            // Fetch the latest 6 news items
            $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC LIMIT 6");
            $newsItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($newsItems as $news): ?>
                <div class="news-card" data-aos="fade-up">
                    <?php if($news['image_path']): ?>
                        <!-- Display the image if it exists -->
                        <div class="news-image">
                            <img src="<?= htmlspecialchars($news['image_path']) ?>"
                                 alt="<?= htmlspecialchars($news['title']) ?>"
                                 class="preview-image"
                                 onclick="openImagePreview(this.src)">
                        </div>
                    <?php endif; ?>
                    <!-- Display the news date -->
                    <div class="news-date"><?= date('F d, Y', strtotime($news['date'])) ?></div>
                    <!-- Display the news title -->
                    <h3><?= htmlspecialchars($news['title']) ?></h3>
                    <!-- Display the news description -->
                    <p><?= htmlspecialchars($news['description']) ?></p>
                    <!-- Link to the full news article -->
                    <a href="news_detail.php?id=<?= $news['id'] ?>" class="read-more">Read More</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Admission Section -->
    <section id="admission" class="admission-section">
        <h2>Admission Information</h2>
        <div class="admission-content">
            <div class="criteria-card" data-aos="fade-up">
                <h3>Eligibility Criteria</h3>
                <ul>
                    <li>Minimum 85% in Mathematics</li>
                    <li>Strong analytical skills</li>
                    <li>English proficiency</li>
                </ul>
            </div>
            <div class="process-card" data-aos="fade-up">
                <h3>Application Process</h3>
                <ol>
                    <li>Online Application</li>
                    <li>Document Verification</li>
                    <li>Entrance Exam</li>
                    <li>Personal Interview</li>
                </ol>
            </div>
        </div>
    </section>
<!-- Contact Section -->
<section id="contact" class="contact">
    <h2 data-aos="fade-up">Get in Touch</h2>
    <?php
    require_once 'config/database.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $message = htmlspecialchars($_POST['message']);
            $status = 'unread'; // Default status
            $created_at = date('Y-m-d H:i:s'); // Current timestamp
            
            // Insert into the messages table
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, status, created_at) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$name, $email, $message, $status, $created_at]);
            
            if ($result) {
                echo '<div id="success-message" class="success-message">Message sent successfully!</div>';
            }
        } catch (PDOException $e) {
            // Log error for admin
            error_log("Database Error: " . $e->getMessage());
            echo '<div class="error-message">Unable to send message. Please try again later.</div>';
        }
    }
    ?>
    <div class="contact-form" data-aos="fade-up">
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <textarea name="message" placeholder="Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
</section>

<!-- JavaScript to hide the success message after 3 seconds -->
<script>
    setTimeout(function() {
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000); // 3000 milliseconds = 3 seconds
</script>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <p>© 2024 Silicon Valley College. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="script.js"></script>
    <div id="imagePreviewModal" class="modal">
        <span class="close-modal">&times;</span>
        <img id="fullSizeImage" class="modal-content">
    </div>
</body>
</html>