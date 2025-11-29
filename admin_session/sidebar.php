<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="toggleSidebar()" title="Tutup menu">
        <i class="bi bi-x-lg"></i>
    </button>
    
    <div class="profile">
        <img src="../assets/img/logosekolah.png" alt="Admin" onerror="this.src='https://via.placeholder.com/50'">
        <div class="profile-info">
            <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
            <p>Administrator</p>
        </div>
    </div>
    
    <nav class="menu">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="gallery.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
            <i class="bi bi-images"></i>
            <span>Galeri Foto</span>
        </a>
        <a href="video.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'video.php' ? 'active' : ''; ?>">
            <i class="bi bi-camera-video"></i>
            <span>Galeri Video</span>
        </a>
        <a href="pendidik_admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'pendidik_admin.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Data Pendidik</span>
        </a>
        <a href="sarana_prasarana.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'sarana_prasarana.php' ? 'active' : ''; ?>">
            <i class="bi bi-building"></i>
            <span>Sarana Prasarana</span>
        </a>
        <a href="eskul.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'eskul.php' ? 'active' : ''; ?>">
            <i class="bi bi-star"></i>
            <span>Ekstrakurikuler</span>
        </a>
        <a href="kontak.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kontak.php' ? 'active' : ''; ?>">
            <i class="bi bi-envelope"></i>
            <span>Pesan Kontak</span>
        </a>
        <a href="edit_profil.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'edit_profil.php' ? 'active' : ''; ?>">
            <i class="bi bi-building"></i>
            <span>Profil Sekolah</span>
        </a>
        <a href="logout.php" style="margin-top: auto; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>
    </nav>
</aside>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  sidebar.classList.toggle('active');
  overlay.classList.toggle('active');
}

// Close sidebar when clicking on a link
document.querySelectorAll('.sidebar .menu a').forEach(link => {
  link.addEventListener('click', function() {
    if (window.innerWidth <= 992) {
      toggleSidebar();
    }
  });
});

// Close sidebar when pressing Escape key
document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    if (sidebar.classList.contains('active')) {
      toggleSidebar();
    }
  }
});
</script>
