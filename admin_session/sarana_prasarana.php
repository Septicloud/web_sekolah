<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// AMBIL SEMUA FOTO
$stmt = mysqli_prepare($koneksi, "SELECT id, nama_sarana, deskripsi, file_foto, created_at FROM sarana_prasarana ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$photos = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sarana Prasarana</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

    <!-- CONTENT -->
    <main class="content">
        <!-- Topbar -->
        <div class="topbar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>📸 Kelola Sarana Prasarana</h1>
            </div>
            <a href="../sarana_prasarana.php" class="btn btn-primary" target="_blank">
                <i class="fas fa-eye"></i> Lihat Sarana Prasarana
            </a>
        </div>

        <!-- Alert Success/Error -->
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            switch ($_GET['success']) {
                case 'added': echo '✅ Foto berhasil ditambahkan!'; break;
                case 'updated': echo '✅ Foto berhasil diupdate!'; break;
                case 'deleted': echo '✅ Foto berhasil dihapus!'; break;
            }
            ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            switch ($_GET['error']) {
                case 'invalid_file': echo '❌ File tidak valid! Hanya JPG, PNG, GIF max 5MB yang diperbolehkan.'; break;
                case 'upload_failed': echo '❌ Upload gagal! Silakan coba lagi.'; break;
                case 'update_failed': echo '❌ Update gagal! Silakan coba lagi.'; break;
                case 'delete_failed': echo '❌ Hapus gagal! Silakan coba lagi.'; break;
            }
            ?>
        </div>
        <?php endif; ?>

        <!-- Tombol Tambah -->
        <div style="margin-bottom: 24px;">
            <button class="btn btn-success" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Tambah Baru
            </button>
        </div>

        <!-- Grid Foto -->
        <div class="card">
            <h2>Daftar Sarana Prasarana (<?= count($photos) ?>)</h2>
            
            <?php if (empty($photos)): ?>
                <div style="text-align: center; padding: 40px 20px; color: #999;">
                    <i class="fas fa-images" style="font-size: 64px; opacity: 0.3;"></i>
                    <p style="margin-top: 16px;">Belum ada foto di galeri</p>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-card">
                            <img src="uploads/sarana_prasarana/<?= htmlspecialchars($photo['file_foto']) ?>" 
                                 alt="<?= htmlspecialchars($photo['nama_sarana']) ?>"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                            <div class="body">
                                <h5><?= htmlspecialchars($photo['nama_sarana']) ?></h5>
                                <p><?= htmlspecialchars(substr($photo['deskripsi'], 0, 80)) ?><?= strlen($photo['deskripsi']) > 80 ? '...' : '' ?></p>
                                <small style="color: #999;">
                                    <i class="far fa-clock"></i> 
                                    <?= date('d M Y', strtotime($photo['created_at'])) ?>
                                </small>
                                <div class="actions" style="margin-top: 12px;">
                                    <button class="btn btn-primary btn-sm" onclick='editPhoto(<?= json_encode($photo) ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deletePhoto(<?= $photo['id'] ?>, '<?= htmlspecialchars(addslashes($photo['nama_sarana'])) ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- MODAL TAMBAH FOTO -->
    <div class="modal-bg" id="addModal">
        <div class="modal">
            <h3>➕ Tambah Baru</h3>
            <form action="upload_sarana.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label>Nama Sarana Prasarana *</label>
                    <input type="text" name="nama_sarana" class="form-control" required placeholder="Contoh: Kegiatan Belajar">
                </div>
                
                <div>
                    <label>Deskripsi *</label>
                    <textarea name="deskripsi" class="form-control" required placeholder="Deskripsi singkat tentang foto..."></textarea>
                </div>
                
                <div>
                    <label>Upload Foto * (JPG, PNG max 5MB)</label>
                    <input type="file" name="file_foto" class="form-control" accept="image/*" required>
                    <small style="color: #666;">Format: JPG, PNG | Ukuran maksimal: 5MB</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('addModal')">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT FOTO -->
    <div class="modal-bg" id="editModal">
        <div class="modal">
            <h3>✏️ Edit Foto</h3>
            <form action="update_sarana.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                
                <div>
                    <label>Nama Sarana Prasarana *</label>
                    <input type="text" name="nama_sarana" id="edit_nama" class="form-control" required>
                </div>
                
                <div>
                    <label>Deskripsi *</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control" required></textarea>
                </div>
                
                <div>
                    <label>Foto Saat Ini:</label>
                    <img id="edit_preview" src="" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                </div>
                
                <div>
                    <label>Ganti Foto (Opsional)</label>
                    <input type="file" name="file_foto" class="form-control" accept="image/*">
                    <small style="color: #666;">Kosongkan jika tidak ingin mengganti foto</small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeModal('editModal')">
                        <i class="fas fa-times"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>


        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.querySelectorAll('.modal-bg').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Edit Photo Function
        function editPhoto(photo) {
            document.getElementById('edit_id').value = photo.id;
            document.getElementById('edit_nama').value = photo.nama_sarana;
            document.getElementById('edit_deskripsi').value = photo.deskripsi;
            document.getElementById('edit_preview').src = 'uploads/sarana_prasarana' + photo.file_foto;
            openModal('editModal');
        }

        // Delete Photo Function
        function deletePhoto(id, nama) {
            if (confirm('Apakah Anda yakin ingin menghapus foto "' + nama + '"?\n\nTindakan ini tidak dapat dibatalkan!')) {
                // Buat form untuk submit delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_sarana.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto hide alert after 3 seconds
        setTimeout(function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>

    <style>
        /* Alert Style */
        .alert {
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
            border-left: 4px solid #198754;
        }
        .alert-danger {
            background: #f8d7da;
            color: #842029;
            border-left: 4px solid #dc3545;
        }
        
        /* Photo Card Improvements */
        .photo-card {
            position: relative;
        }
        .photo-card img {
            cursor: pointer;
            transition: transform 0.3s;
        }
        .photo-card:hover img {
            transform: scale(1.05);
        }
        
        /* Responsive images */
        @media (max-width: 768px) {
            .photo-card img {
                height: 180px;
            }
        }
        
        @media (max-width: 600px) {
            .photo-card img {
                height: 150px;
            }
        }
    </style>
</body>
</html>