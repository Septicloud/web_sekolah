<?php
include 'admin_session/koneksi.php';
$result = mysqli_query($koneksi, "SELECT * FROM pendidik ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SLB Roza - Beranda</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="CSS/style.css" rel="stylesheet">
</head>
<body>
  <?php include 'partials/header.php'; ?>

  <div class="container my-5">
    <h2 class="mb-4">Tenaga Pendidik</h2>
    <div class="row g-4">
      <?php if(mysqli_num_rows($result) === 0): ?>
        <p>Belum ada data pendidik.</p>
      <?php else: ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-md-4">
            <div class="card h-100 shadow-sm">
              <img src="uploads/pendidik/<?= htmlspecialchars($row['foto']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama']) ?>">
              <div class="card-body text-center">
                <h5 class="card-title"><?= htmlspecialchars($row['nama']) ?></h5>
                <p class="card-text text-muted"><?= htmlspecialchars($row['jabatan']) ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>
  </div>

  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
