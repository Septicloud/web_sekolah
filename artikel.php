<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artikel SLB Roza</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="CSS/style.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/style-artikel.css">
</head>
<body>

  <?php include 'partials/header.php'; ?>

<?php
// FEED BLOGGER JSON
$feed_url = "https://slbrozasoreang.blogspot.com/feeds/posts/default?alt=json&max-results=50";

// SISTEM CACHE
$cache_file = __DIR__ . "/cache/blog_cache.json";
$cache_expire = 60 * 10; // 10 menit

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_expire) {
    $json = file_get_contents($cache_file);
} else {
    $json = @file_get_contents($feed_url);
    if ($json !== false) {
        file_put_contents($cache_file, $json);
    }
}

$data = json_decode($json, true);

if (!$data || !isset($data['feed']['entry'])) {
    echo "<p>Gagal memuat artikel dari Blogger.</p>";
    include 'partials/footer.php';
    exit;
}

$entries = $data['feed']['entry'];
$max_articles = 6;

echo '<section class="article-section">
        <div class="container my-5">
            <h2>Artikel Terbaru</h2>
            <div class="article-grid">';

$count = 0;

foreach ($entries as $post) {
    if ($count >= $max_articles) break;

    // Judul
    $title = $post['title']['$t'];

    // Ambil link artikel (alternate link)
    $link = $post['link'][4]['href'] ?? ""; 
// index 4 hampir selalu link artikel asli


    if ($link == "") continue;

    // Tanggal
    $published = date("d M Y", strtotime($post['published']['$t']));

    // Konten
    $content = isset($post['content']['$t']) ? $post['content']['$t'] : "";
    $plain_text = strip_tags($content);
    $preview = substr($plain_text, 0, 200) . "...";

    // Thumbnail
    $thumbnail = "https://via.placeholder.com/400x250?text=No+Image";
    if (preg_match('/<img[^>]+src="([^">]+)"/', $content, $match)) {
        $thumbnail = $match[1];
    }

    echo '
        <a href="artikel_detail.php?url='.urlencode($link).'" class="article-card">
            <img src="'.$thumbnail.'" alt="'.$title.'" class="article-thumb">
            <div class="article-content">
                <h3>'.$title.'</h3>
                <p>'.$preview.'</p>
                <span class="article-date">'.$published.'</span>
                <span class="readmore">Baca Selengkapnya →</span>
            </div>
        </a>
    ';

    $count++;
}

echo '</div></div></section>';

include 'partials/footer.php';
?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
