<?php
require_once 'config/database.php';

// Get news article by ID
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch();

// Redirect if news item not found
if (!$news) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($news['title']) ?> - Silicon Valley College</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* News Detail Page Styling */
        .news-detail {
            max-width: 800px;
            margin: 80px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .news-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .news-header h1 {
            font-size: 2.5rem;
            color: #214780;
            margin-bottom: 10px;
        }

        .news-date {
            color: #666;
            font-size: 0.9em;
            font-style: italic;
        }

        .news-image {
            margin-bottom: 30px;
            text-align: center; /* Center the image */
        }

        .news-image img {
            max-width: 100%; /* Ensure the image is responsive */
            height: auto; /* Maintain aspect ratio */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .news-image img:hover {
            transform: scale(1.02); /* Slight zoom on hover */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .news-content {
            line-height: 1.8;
            margin: 20px 0;
            color: #333;
            font-size: 1.1rem;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        .back-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="news-detail">
        <div class="news-header">
            <h1><?= htmlspecialchars($news['title']) ?></h1>
            <div class="news-date"><?= date('F d, Y', strtotime($news['date'])) ?></div>
        </div>
        
        <!-- Display the image if it exists -->
        <?php if ($news['image_path']): ?>
            <div class="news-image">
                <img src="<?= htmlspecialchars($news['image_path']) ?>"
                     alt="<?= htmlspecialchars($news['title']) ?>">
            </div>
        <?php endif; ?>
        
        <div class="news-content">
            <?= nl2br(htmlspecialchars($news['content'])) ?>
        </div>
        
        <a href="index.php#news" class="back-button">Back to News</a>
    </div>
</body>
</html>