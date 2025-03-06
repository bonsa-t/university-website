<?php
require_once 'config/database.php';

function getLatestNews() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC LIMIT 3");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Replace the static news section with:
?>
<section id="news-events" class="news-section">
    <h2>Latest Updates</h2>
    <div class="news-grid">
        <?php
        $newsItems = getLatestNews();
        foreach($newsItems as $news): ?>
            <div class="news-card" data-aos="fade-up">
                <div class="news-date"><?php echo date('F d, Y', strtotime($news['date'])); ?></div>
                <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                <p><?php echo htmlspecialchars($news['description']); ?></p>
                <a href="news_detail.php?id=<?php echo $news['id']; ?>" class="read-more">Read More</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
