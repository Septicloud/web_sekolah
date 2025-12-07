<?php
include 'admin_session/koneksi.php';

$result = mysqli_query($koneksi, "SELECT * FROM pendidik ORDER BY created_at DESC");

// Hitung jumlah pendidik
$total = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SLB Roza - Pendidik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="CSS/style.css" rel="stylesheet">
</head>
<body>
  <?php include 'partials/header.php'; ?>

  <div class="container my-5">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>

      <!-- FOTO ORGANIGRAM -->
      <div class="card shadow-sm mb-5">
        <h4 class="card-title text-center mb-5">Struktur Organigram SLB BC Roudhotul Zannah</h4>
          <img src="uploads/pendidik/<?= $row['foto'] ?>"
               class="card-img-top w-100"
               style="max-height: 900px; object-fit: contain;"
               alt="Struktur Organigram">

          <div class="card-body text-center">
              <p class="text-muted">Struktur organisasi sekolah</p>
              <p><b>Total Pendidik: <?= $total ?></b></p>
          </div>
          <?php endwhile; ?>
      </div>
      <!-- END ORGANIGRAM -->

  </div>

  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
