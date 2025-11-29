<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 2. Proses simpan perubahan profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $berhasil_update = true;

    // Bagian yang diharapkan ada di form
    $expected_fields = ['sejarah', 'visi', 'misi'];

    foreach ($expected_fields as $bagian) {
        if (isset($_POST[$bagian])) {
            // Bersihkan input menggunakan fungsi real_escape_string sebelum digunakan
            // Walaupun prepared statement melindungi dari SQL Injection, pembersihan dasar tetap baik
            $isi = htmlspecialchars($_POST[$bagian], ENT_QUOTES, 'UTF-8'); 

            // Gunakan prepared statement untuk keamanan SQL Injection
            $stmt = mysqli_prepare($koneksi, "UPDATE profil SET isi = ? WHERE bagian = ?");

            // 'ss' berarti dua parameter berikutnya adalah string
            mysqli_stmt_bind_param($stmt, "ss", $isi, $bagian);
            
            if (!mysqli_stmt_execute($stmt)) {
                $berhasil_update = false;
                // Anda bisa log error di sini
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($berhasil_update) {
        echo "<script>
                  alert('Profil berhasil diperbarui!');
                  window.location.href = 'edit_profil.php';
              </script>";
    } else {
         echo "<script>
                  alert('Gagal memperbarui beberapa bagian profil!');
                  window.location.href = 'edit_profil.php';
              </script>";
    }
    exit;
}

// 3. Ambil data dari tabel profil untuk ditampilkan di form
$query = "SELECT bagian, isi FROM profil";
$result = mysqli_query($koneksi, $query);
$profil = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Gunakan str_replace untuk mengatasi masalah enter/baris baru dari database
        $profil[$row['bagian']] = str_replace(array("\r\n", "\r", "\n"), '', $row['isi']);
    }
} else {
    // Ini akan menampilkan error jika query gagal
    die("Gagal mengambil data profil: " . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil SLB Roza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ... (CSS Anda dipertahankan, tidak perlu diubah) ... */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Poppins", "Segoe UI", Roboto, sans-serif; background-color: #f8f9fa; color: #333; line-height: 1.7; }
        .container { max-width: 900px; margin: 60px auto; background: #fff; padding: 40px 50px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08); }
        h2 { font-weight: 700; color: #0d6efd; margin-bottom: 10px; position: relative; }
        h2::after { content: ""; display: block; width: 60px; height: 3px; background: #0d6efd; margin-top: 6px; border-radius: 2px; }
        p { font-size: 1rem; margin-bottom: 25px; text-align: justify; }
        @media (max-width: 768px) {
            .container { padding: 25px 20px; margin: 30px 10px; }
            h2 { font-size: 1.4rem; }
            p { font-size: 0.95rem; }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <main class="content"><div class="container">
        <h2 class="mb-4">Edit Profil SLB Roza</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Sejarah</label>
                <textarea name="sejarah" class="form-control" rows="4" required><?= htmlspecialchars_decode($profil['sejarah'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Visi</label>
                <textarea name="visi" class="form-control" rows="3" required><?= htmlspecialchars_decode($profil['visi'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Misi</label>
                <textarea name="misi" class="form-control" rows="5" required><?= htmlspecialchars_decode($profil['misi'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </form>
    </div></main>
</body>
</html>