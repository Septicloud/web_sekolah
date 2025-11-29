<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// ambil total foto & video
$qFoto = mysqli_query($koneksi, "SELECT COUNT(*) AS total_foto FROM photos");
$total_foto = mysqli_fetch_assoc($qFoto)['total_foto'];

$qVideo = mysqli_query($koneksi, "SELECT COUNT(*) AS total_video FROM galeri_video");
$total_video = mysqli_fetch_assoc($qVideo)['total_video'];

$qPendidik = mysqli_query($koneksi, "SELECT COUNT(*) AS total_pendidik FROM pendidik");
$total_pendidik = mysqli_fetch_assoc($qPendidik)['total_pendidik'];

$qEskul = mysqli_query($koneksi, "SELECT COUNT(*) AS total_eskul FROM eskul");
$total_eskul = mysqli_fetch_assoc($qEskul)['total_eskul'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Admin SLB Roza</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body class="admin-layout">
  <?php include 'sidebar.php'; ?>

  <main class="content">
    <div class="topbar">
      <div>
        <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
        <p style="color:#6b7280; margin: 0;">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! Lihat ringkasan data yang telah Anda kelola.</p>
      </div>
      <button class="menu-toggle" id="menuToggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
      </button>
    </div>

    <div class="dashboard-container">
      <div class="card-stat bg-foto">
        <div>
          <h2><?php echo $total_foto; ?></h2>
          <p>Total Foto</p>
        </div>
        <i class="bi bi-image-fill icon"></i>
      </div>

      <div class="card-stat bg-video">
        <div>
          <h2><?php echo $total_video; ?></h2>
          <p>Total Video</p>
        </div>
        <i class="bi bi-camera-video-fill icon"></i>
      </div>

      <div class="card-stat bg-guru">
        <div>
          <h2><?php echo $total_pendidik; ?></h2>
          <p>Total Pendidik</p>
        </div>
        <i class="bi bi-person-badge-fill icon"></i>
      </div>

      <div class="card-stat" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
        <div>
          <h2><?php echo $total_eskul; ?></h2>
          <p>Total Ekstrakurikuler</p>
        </div>
        <i class="bi bi-star-fill icon"></i>
      </div>
    </div>

    <!-- Statistik Tambahan -->
    <div class="row mt-5">
      <div class="col-lg-6">
        <div class="card">
          <h3><i class="bi bi-graph-up"></i> Ringkasan Konten</h3>
          <div style="padding: 20px 0;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Foto</span>
              <strong><?php echo $total_foto; ?> item</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Video</span>
              <strong><?php echo $total_video; ?> item</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Pendidik</span>
              <strong><?php echo $total_pendidik; ?> orang</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0;">
              <span style="color: #6b7280;">Ekstrakurikuler</span>
              <strong><?php echo $total_eskul; ?> program</strong>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <h3><i class="bi bi-info-circle"></i> Informasi Sistem</h3>
          <div style="padding: 20px 0;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Status</span>
              <span style="background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">Aktif</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Versi</span>
              <strong>1.0.0</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
              <span style="color: #6b7280;">Terakhir Diperbarui</span>
              <strong><?php echo date('d/m/Y'); ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 12px 0;">
              <span style="color: #6b7280;">Admin Terdaftar</span>
              <strong>1 pengguna</strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Akses Cepat -->
    <div class="card mt-5">
      <h3><i class="bi bi-lightning-fill"></i> Akses Cepat</h3>
      <div class="row g-3" style="margin-top: 0;">
        <div class="col-sm-6 col-md-3">
          <a href="gallery.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <i class="bi bi-images"></i> Kelola Foto
          </a>
        </div>
        <div class="col-sm-6 col-md-3">
          <a href="video.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <i class="bi bi-camera-video"></i> Kelola Video
          </a>
        </div>
        <div class="col-sm-6 col-md-3">
          <a href="pendidik_admin.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <i class="bi bi-people"></i> Kelola Pendidik
          </a>
        </div>
        <div class="col-sm-6 col-md-3">
          <a href="kontak.php" class="btn btn-primary" style="width: 100%; justify-content: center;">
            <i class="bi bi-envelope"></i> Pesan Kontak
          </a>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.querySelector('.overlay');
      sidebar.classList.toggle('active');
      overlay.classList.toggle('active');
    }

    // Tampilkan menu toggle hanya di mobile
    window.addEventListener('resize', function() {
      const menuToggle = document.getElementById('menuToggle');
      if (window.innerWidth > 992) {
        menuToggle.style.display = 'none';
      } else {
        menuToggle.style.display = 'inline-flex';
      }
    });

    // Initial check
    const menuToggle = document.getElementById('menuToggle');
    if (window.innerWidth > 992) {
      menuToggle.style.display = 'none';
    }
  </script>
</body>
</html>
