<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SLB Roza - Beranda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <!-- Header -->
  <?php include 'partials/header.php'; ?>

  <!-- Home Section -->
  <section id="home" class="hero">
    <div class="overlay"></div>
    <div class="hero-content">
      <h2>Selamat Datang di SLB Roza</h2>
      <p>Sekolah inklusif dengan visi membangun generasi mandiri dan berprestasi.</p>
      <a href="profil.php" class="btn btn-lg" style="background: white; color: #0d6efd; font-weight: 700;">Pelajari Lebih Lanjut</a>
    </div>
  </section>

  <!-- Ringkasan Profil -->
  <section class="container my-5">
    <div class="card">
      <h2>Profil Singkat</h2>
      <p style="font-size: 1.05rem; color: #6b7280; line-height: 1.8; margin-bottom: 20px;">
        SLB Roza didirikan untuk mendukung pendidikan anak berkebutuhan khusus dengan memberikan layanan pendidikan yang inklusif, berkualitas, dan berorientasi pada pengembangan potensi setiap peserta didik.
      </p>
      <a href="profil.php" class="btn btn-primary">Baca Selengkapnya</a>
    </div>
  </section>

  <!-- Fitur Unggulan -->
  <section class="container mb-5">
    <div class="text-center mb-5">
      <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 16px;">Fitur Unggulan Kami</h2>
      <p style="font-size: 1.1rem; color: #6b7280;">Komitmen kami untuk memberikan pendidikan terbaik</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card text-center" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
          <div style="padding: 30px 20px;">
            <i class="bi bi-people-fill" style="font-size: 3rem; color: #0d6efd; margin-bottom: 16px;"></i>
            <h4 style="font-weight: 700; margin-bottom: 12px;">Pendidik Berpengalaman</h4>
            <p style="color: #6b7280;">Tim pendidik profesional yang berdedikasi untuk kesuksesan setiap siswa.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
          <div style="padding: 30px 20px;">
            <i class="bi bi-building" style="font-size: 3rem; color: #10b981; margin-bottom: 16px;"></i>
            <h4 style="font-weight: 700; margin-bottom: 12px;">Sarana Lengkap</h4>
            <p style="color: #6b7280;">Fasilitas modern dan lengkap untuk mendukung proses pembelajaran.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
          <div style="padding: 30px 20px;">
            <i class="bi bi-star-fill" style="font-size: 3rem; color: #f59e0b; margin-bottom: 16px;"></i>
            <h4 style="font-weight: 700; margin-bottom: 12px;">Program Unggulan</h4>
            <p style="color: #6b7280;">Program pembelajaran yang disesuaikan dengan kebutuhan setiap siswa.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
