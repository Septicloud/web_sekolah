<?php
include 'admin_session/koneksi.php';

// Ambil semua bagian profil
$query = "SELECT bagian, isi FROM profil";
$result = mysqli_query($koneksi, $query);

$profil = [];
while ($row = mysqli_fetch_assoc($result)) {
    $profil[$row['bagian']] = $row['isi'];
}

// --- FUNGSI FORMATTER TEKS ---
// Fungsi ini akan mendeteksi Enter dan tanda list (- atau *)
function formatText($text) {
    if (empty($text)) return 'Belum ada data.';

    // 1. Pecah teks berdasarkan tombol Enter (Baris baru)
    $lines = explode("\n", $text);
    
    $output = '';
    $inList = false; // Penanda apakah kita sedang di dalam <ul>

    foreach ($lines as $line) {
        $line = trim($line); // Hapus spasi berlebih di awal/akhir
        if (empty($line)) continue; // Lewati baris kosong

        // 2. Deteksi apakah baris dimulai dengan "-" atau "*"
        if (substr($line, 0, 1) === '-' || substr($line, 0, 1) === '*') {
            // Jika belum masuk mode list, buka tag <ul>
            if (!$inList) {
                $output .= '<ul style="margin-bottom: 1rem;">';
                $inList = true;
            }
            // Hapus tanda - atau * di depan, lalu bungkus dengan <li>
            $cleanContent = substr($line, 1); 
            $output .= "<li>" . htmlspecialchars($cleanContent) . "</li>";
        } else {
            // Jika sebelumnya mode list, tutup tag </ul>
            if ($inList) {
                $output .= '</ul>';
                $inList = false;
            }
            // Tampilkan sebagai paragraf biasa
            $output .= "<p>" . nl2br(htmlspecialchars($line)) . "</p>";
        }
    }

    // Tutup tag list jika masih terbuka di akhir
    if ($inList) {
        $output .= '</ul>';
    }

    return $output;
}
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
    <div class="mb-5">
        <h2 class="border-bottom pb-2 mb-3">Sejarah</h2>
        <div class="content-text">
            <?= formatText($profil['sejarah'] ?? '') ?>
        </div>
    </div>

    <div class="mb-5">
        <h2 class="border-bottom pb-2 mb-3">Visi</h2>
        <div class="content-text">
            <?= formatText($profil['visi'] ?? '') ?>
        </div>
    </div>

    <div class="mb-5">
        <h2 class="border-bottom pb-2 mb-3">Misi</h2>
        <div class="content-text">
            <?= formatText($profil['misi'] ?? '') ?>
        </div>
    </div>
  </div>

  <?php include 'partials/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>