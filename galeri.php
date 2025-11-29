<?php
include 'admin_session/koneksi.php';

// Get all videos
$query = "SELECT * FROM galeri_video ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);

// Function to convert YouTube URL to embed
function getYouTubeEmbedUrl($url) {
    $video_id = '';
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    }
    return $video_id ? "https://www.youtube.com/embed/{$video_id}" : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Video - SLB Roza</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="CSS/style.css">

</head>
<body>
    <?php include 'partials/header.php'; ?>
    
    <div class="container my-5">
    <h2 class="mb-4">Galeri Foto</h2>
    <p>Dokumentasi kegiatan dan prestasi SLB Roza</p>

    <div class="photo-grid">
        <?php
        $foto = mysqli_query($koneksi, "SELECT * FROM photos ORDER BY created_at DESC");
        if (mysqli_num_rows($foto) > 0):
            while ($f = mysqli_fetch_assoc($foto)):
        ?>
            <div class="photo-card">
                <img src="uploads/galeri/<?= $f['file_foto'] ?>" alt="<?= $f['nama_foto'] ?>">
                <div class="photo-info">
                    <h3><?= htmlspecialchars($f['nama_foto']) ?></h3>
                    <p><?= htmlspecialchars($f['deskripsi']) ?></p>
                    <span><i class="far fa-clock"></i> <?= date('d F Y', strtotime($f['created_at'])) ?></span>
                </div>
            </div>

        <?php endwhile; else: ?>
            <p class="text-center">Belum ada foto.</p>
        <?php endif; ?>
    </div>
</div>

    
    <div class="container">
        <h2 class="mb-4">Galeri Video</h2>
    <p>Dokumentasi kegiatan dan prestasi SLB Roza</p>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="video-grid">
                <?php while ($video = mysqli_fetch_assoc($result)): ?>
                    <div class="video-card">
                        <div class="video-embed">
                            <iframe 
                                src="<?= getYouTubeEmbedUrl($video['youtube_url']) ?>" 
                                allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                            </iframe>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title"><?= htmlspecialchars($video['judul']) ?></h3>
                            <p class="video-description"><?= htmlspecialchars($video['deskripsi']) ?></p>
                            <div class="video-date">
                                <i class="far fa-clock"></i>
                                <?= date('d F Y', strtotime($video['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 1rem;">
                <i class="fab fa-youtube" style="font-size: 5rem; color: #ddd;"></i>
                <p style="color: #999; font-size: 1.25rem; margin-top: 1rem;">Belum ada video</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'partials/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>