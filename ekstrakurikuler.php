<?php
include 'admin_session/koneksi.php';

// Get all videos
$query = "SELECT * FROM eskul ORDER BY created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekstrakurikuler - SLB Roza</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="CSS/style.css" rel="stylesheet">

    <style>
        .sarana-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .sarana-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .sarana-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .sarana-info {
            padding: 15px;
        }
        .sarana-title {
            font-size: 20px;
            font-weight: bold;
        }
        .sarana-date {
            font-size: 14px;
            color: #777;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="container my-5">
    <h2 class="mb-4">Ekstrakurikuler</h2>
    <p>Ekstrakurikuler di SLB Roudhotul Zannah</p>
</div>

<div class="container">
    <?php if (mysqli_num_rows($result) > 0): ?>

        <div class="sarana-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <div class="sarana-card">

                    <?php if (!empty($row['file_foto'])): ?>
                        <img src="admin_session/uploads/eskul/<?= $row['file_foto'] ?>">

                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x220?text=No+Image" alt="No Image">
                    <?php endif; ?>

                    <div class="sarana-info">
                        <div class="sarana-title"><?= htmlspecialchars($row['nama_eskul']) ?></div>
                        <p><?= htmlspecialchars($row['deskripsi']) ?></p>

                        <div class="sarana-date">
                            <i class="far fa-clock"></i>
                            <?= date('d F Y', strtotime($row['created_at'])) ?>
                        </div>
                    </div>

                </div>

            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div class="text-center" style="padding: 4rem 1rem;">
            <i class="fa-solid fa-image" style="font-size: 5rem; color: #ccc;"></i>
            <p style="color: #999; font-size: 1.2rem; margin-top: 1rem;">Belum ada data Ekstrakurikuler</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>