<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil semua data pesan baru, urutkan dari yang terbaru
$stmt = mysqli_prepare($koneksi, "SELECT id, nama, email, pesan, status, tanggal_kirim, tanggal_dibaca, ip_address, user_agent FROM kontak ORDER BY tanggal_kirim DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pesan_masuk = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Dashboard - Pesan Masuk</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        /* Tambahkan ini untuk styling status badge */
        .status-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status-baru {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .status-selesai {
            background-color: #e8f5e9;
            color: #388e3c;
        }
        .table-wrapper {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            white-space: nowrap; /* Agar tabel tidak terlalu berantakan */
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.875rem;
            margin-right: 5px;
        }
        /* Style untuk modal detail */
        .modal-detail-grid {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 5px 10px;
        }
        .modal-detail-grid strong {
            text-align: right;
        }
        #modalPesanIsi {
            background: #f9f9f9; 
            padding: 10px; 
            border-left: 3px solid #ccc; 
            margin-top: 5px; 
            white-space: pre-wrap; /* Jaga format pesan */
            word-wrap: break-word; /* Agar pesan panjang tidak merusak modal */
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

    <main class="content">
        <div class="topbar">
            <div>
                <h1>Pesan Masuk</h1>
                <p style="color:#6b7280">Kelola pesan yang dikirim oleh pengunjung.</p>
            </div>
        </div>

        <section class="card">
            <h3>Daftar Pesan</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tanggal Kirim</th>
                            <th>Tanggal Dibaca</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($pesan_masuk)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">Belum ada pesan masuk.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($pesan_masuk as $index => $pesan): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($pesan['nama']) ?></td>
                                    <td><?= htmlspecialchars($pesan['email']) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($pesan['status']) ?>">
                                            <?= ucfirst(htmlspecialchars($pesan['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])) ?></td>
                                    <td>
                                        <?= $pesan['tanggal_dibaca'] ? date('d M Y, H:i', strtotime($pesan['tanggal_dibaca'])) : '-' ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm" onclick="openDetail(
                                            '<?= htmlspecialchars(addslashes($pesan['nama'])) ?>',
                                            '<?= htmlspecialchars(addslashes($pesan['email'])) ?>',
                                            '<?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])) ?>',
                                            '<?= htmlspecialchars(addslashes($pesan['pesan'])) ?>',
                                            '<?= htmlspecialchars(addslashes($pesan['ip_address'])) ?>',
                                            '<?= htmlspecialchars(addslashes($pesan['user_agent'])) ?>'
                                        )">Lihat</button>
                                        
                                        <?php if ($pesan['status'] == 'baru'): ?>
                                            <a href="update_pesan_status.php?id=<?= $pesan['id'] ?>" class="btn btn-sm btn-primary">Tandai Selesai</a>
                                        <?php endif; ?>

                                        <form action="hapus_pesan.php" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus pesan ini?')">
                                            <input type="hidden" name="id" value="<?= $pesan['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal-bg" id="modal">
        <div class="modal card">
            <h3 id="modalTitle">Detail Pesan</h3>
            
            <div class="modal-detail-grid">
                <strong>Nama:</strong> <span id="modalNama"></span>
                <strong>Email:</strong> <span id="modalEmail"></span>
                <strong>Dikirim:</strong> <span id="modalWaktu"></span>
                <strong>IP Address:</strong> <span id="modalIp"></span>
            </div>
            
            <div style="margin-top:10px">
                <strong>User Agent:</strong>
                <div id="modalUserAgent" style="font-size: 0.8em; color: #555; background: #f9f9f9; padding: 5px; border-radius: 4px; margin-top: 5px;"><?= htmlspecialchars($pesan['user_agent']) ?></div>
            </div>

            <hr style="margin: 15px 0;">
            <div>
                <strong>Pesan:</strong>
                <blockquote id="modalPesanIsi"></blockquote>
            </div>

            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="btn" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal');

        function closeModal(){ modal.style.display = 'none'; }

        function openDetail(nama, email, waktu, pesan, ip, userAgent){
            document.getElementById('modalNama').textContent = nama;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalWaktu').textContent = waktu;
            document.getElementById('modalPesanIsi').textContent = pesan;
            document.getElementById('modalIp').textContent = ip;
            document.getElementById('modalUserAgent').textContent = userAgent;
            modal.style.display = 'flex';
        }

        modal.addEventListener('click', (e)=>{ if (e.target === modal) closeModal(); });
    </script>
</body>
</html>