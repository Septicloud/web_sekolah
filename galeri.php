<?php
include 'admin_session/koneksi.php';

// Ambil data video
$query_video = "SELECT * FROM galeri_video ORDER BY created_at DESC";
$result_video = mysqli_query($koneksi, $query_video);

// Ambil data foto
$query_foto = "SELECT * FROM photos ORDER BY created_at DESC";
$result_foto = mysqli_query($koneksi, $query_foto);

// Fungsi untuk convert URL YouTube jadi embed
function getYouTubeEmbedUrl($url) {
    $video_id = '';
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
        $video_id = $id[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
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
    <title>Galeri - SLB Roza</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1>/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* CSS KHUSUS HALAMAN GALERI (Bisa dipindah ke file style.css) */
        .section-header {
            margin-bottom: 3rem;
            text-align: center;
        }
        .section-header h2 {
            font-weight: 700;
            color: #0e9455;
            margin-bottom: 0.5rem;
        }
        
        /* Grid Layout */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
            margin-bottom: 60px;
        }

        /* Card Styling */
        .gallery-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* Image Styling */
        .img-wrapper {
            position: relative;
            padding-top: 66.66%; /* Aspect Ratio 3:2 */
            overflow: hidden;
        }

        .gallery-card img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-card:hover img {
            transform: scale(1.1);
        }

        /* Video Wrapper (Responsive 16:9) */
        .video-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            background: #000;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Content Styling */
        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
            line-height: 1.4;
        }

        .card-desc {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 15px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            font-size: 0.85rem;
            color: #999;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 12px;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <?php include 'partials/header.php'; ?>
    
    <div class="container my-5">
        <div class="section-header">
            <h2><i class="bi bi-camera-fill"></i> Galeri Foto</h2>
            <p class="text-muted">Dokumentasi kegiatan dan momen berharga di SLB Roza</p>
        </div>

        <div class="gallery-grid">
          <?php
        $foto = mysqli_query($koneksi, "SELECT * FROM photos ORDER BY created_at DESC");
        if (mysqli_num_rows($foto) > 0):
            while ($f = mysqli_fetch_assoc($foto)):
        ?>
                    <div class="gallery-card">
                        <div class="img-wrapper">
                            <img src="uploads/galeri/<?= htmlspecialchars($f['file_foto']) ?>" 
                                 alt="<?= htmlspecialchars($f['nama_foto']) ?>"
                                 onerror="this.src=''">
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($f['nama_foto']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($f['deskripsi']) ?></p>
                            <div class="card-meta">
                                <i class="bi bi-calendar3"></i> 
                                <?= date('d F Y', strtotime($f['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-images" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="mt-3 text-muted">Belum ada foto yang diunggah.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <hr class="container" style="opacity: 0.1;">
    
    <div class="container my-5">
        <div class="section-header">
            <h2><i class="bi bi-play-circle-fill"></i> Galeri Video</h2>
            <p class="text-muted">Cuplikan aktivitas dan kreativitas siswa</p>
        </div>

        <div class="gallery-grid">
            <?php if (mysqli_num_rows($result_video) > 0): ?>
                <?php while ($v = mysqli_fetch_assoc($result_video)): ?>
                    <div class="gallery-card">
                        <div class="video-wrapper">
                            <iframe 
                                src="<?= getYouTubeEmbedUrl($v['youtube_url']) ?>" 
                                title="<?= htmlspecialchars($v['judul']) ?>"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($v['judul']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($v['deskripsi']) ?></p>
                            <div class="card-meta">
                                <i class="bi bi-clock"></i>
                                <?= date('d F Y', strtotime($v['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-film" style="font-size: 3rem; color: #dee2e6;"></i>
                    <p class="mt-3 text-muted">Belum ada video yang diunggah.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="container my-5">
        <div class="section-header">
            <h2><i class="bi bi-play-circle-fill"></i> Galeri Instagram</h2>
            <p class="text-muted">Dokumentasi yang ada di Instagram</p>
        </div>

        <!-- LightWidget WIDGET --><script src="https://cdn.lightwidget.com/widgets/lightwidget.js"></script><iframe src="//lightwidget.com/widgets/1e3726d5a3885799ad3899811d57878c.html" scrolling="no" allowtransparency="true" class="lightwidget-widget" style="width:100%;border:0;overflow:hidden;"></iframe>
        
    </div>
    
    <?php include 'partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>