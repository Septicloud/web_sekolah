<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

// AMBIL SEMUA VIDEO (pastikan tabel punya kolom file_foto & youtube_url)
$stmt = mysqli_prepare($koneksi, "SELECT id, judul, deskripsi, youtube_url, created_at FROM galeri_video ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$videos = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
mysqli_stmt_close($stmt);

// Function to convert YouTube URL to embed format (robust)
function getYouTubeEmbedUrl($url) {
    if (!$url) return '';
    // Try several patterns and parse_url fallback
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtube\.com\/.*[?&]v=)([A-Za-z0-9_-]{11})/i',
        '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i',
        '/youtu\.be\/([A-Za-z0-9_-]{11})/i'
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $url, $m)) return "https://www.youtube.com/embed/{$m[1]}";
    }
    // fallback: try to parse query param v
    $u = parse_url($url);
    if (!empty($u['query'])) {
        parse_str($u['query'], $q);
        if (!empty($q['v'])) {
            $id = substr($q['v'], 0, 11);
            return "https://www.youtube.com/embed/{$id}";
        }
    }
    return '';
}

function getYouTubeThumbnail($url) {
    if (!$url) return '';
    $patterns = [
        '/(?:youtube\.com\/watch\?v=|youtube\.com\/.*[?&]v=)([A-Za-z0-9_-]{11})/i',
        '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i',
        '/youtu\.be\/([A-Za-z0-9_-]{11})/i'
    ];
    foreach ($patterns as $p) {
        if (preg_match($p, $url, $m)) return "https://img.youtube.com/vi/{$m[1]}/maxresdefault.jpg";
    }
    // fallback to default thumbnail size if we can't get maxres
    return '';
}

// helper to safely encode video array for data-* attribute
function json_attr_encode($arr) {
    return htmlspecialchars(json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Galeri Video</title>
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
                <h1>📸 Kelola Galeri Video</h1>
            </div>
            <a href="../galeri.php" class="btn btn-primary" target="_blank">
                <i class="fas fa-eye"></i> Lihat Galeri Publik
            </a>
        </div>

        <!-- Alert Success/Error -->
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            switch ($_GET['success']) {
                case 'added': echo '✅ Item berhasil ditambahkan!'; break;
                case 'updated': echo '✅ Item berhasil diupdate!'; break;
                case 'deleted': echo '✅ Item berhasil dihapus!'; break;
                default: echo '✅ Sukses.'; break;
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
                default: echo '❌ Terjadi kesalahan.'; break;
            }
            ?>
        </div>
        <?php endif; ?>

        <!-- Tombol Tambah -->
        <div style="margin-bottom: 24px;">
            <button class="btn btn-success" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Tambah Item Baru
            </button>
        </div>

        <!-- Grid Video / Foto -->
        <div class="card">
            <h2>Daftar item (<?= is_array($videos) ? count($videos) : 0 ?>)</h2>
            
            <?php if (empty($videos)): ?>
                <div style="text-align: center; padding: 40px 20px; color: #999;">
                    <i class="fas fa-images" style="font-size: 64px; opacity: 0.3;"></i>
                    <p style="margin-top: 16px;">Belum ada item di galeri</p>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($videos as $video): 
                        // Tentukan thumbnail
                        $thumb = '';
                        if (!empty($video['file_foto']) && file_exists(__DIR__ . '/uploads/' . $video['file_foto'])) {
                            $thumb = 'uploads/' . $video['file_foto'];
                        } elseif (!empty($video['youtube_url'])) {
                            $thumb = getYouTubeThumbnail($video['youtube_url']);
                        } else {
                            $thumb = 'https://via.placeholder.com/300x200?text=No+Image';
                        }
                        // safe title & desc
                        $safeTitle = htmlspecialchars($video['judul'] ?? '', ENT_QUOTES, 'UTF-8');
                        $safeDesc = htmlspecialchars($video['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="video-card">
                            <img src="<?= $thumb ?>" 
                                 alt="<?= $safeTitle ?>"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'"
                                 style="width:100%; height:200px; object-fit:cover; border-radius:8px; cursor:pointer;"
                                 onclick="openPreview(<?= htmlspecialchars(json_encode($video['id']), ENT_QUOTES, 'UTF-8') ?>)">
                            <div class="body">
                                <h5><?= $safeTitle ?></h5>
                                <p><?= htmlspecialchars(mb_substr($video['deskripsi'], 0, 80), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($video['deskripsi']) > 80 ? '...' : '' ?></p>
                                <small style="color: #999;">
                                    <i class="far fa-clock"></i> 
                                    <?= date('d M Y', strtotime($video['created_at'])) ?>
                                </small>
                                <div class="actions" style="margin-top: 12px;">
                                    <!-- gunakan data-video untuk menghindari masalah peng-escape-an -->
                                    <button class="btn btn-primary btn-sm" type="button"
                                            data-video='<?= json_attr_encode($video) ?>'
                                            onclick="handleEditBtn(this)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" type="button"
                                            data-id="<?= (int)$video['id'] ?>"
                                            data-title="<?= $safeTitle ?>"
                                            onclick="handleDeleteBtn(this)">
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

    <!-- MODAL TAMBAH ITEM -->
    <div class="modal-bg" id="addModal">
        <div class="modal">
            <h3>➕ Tambah Item Baru</h3>
            <!-- Sesuaikan action ke backend Anda (upload_video.php / upload_foto.php) -->
            <form action="video-add.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label>Judul *</label>
                    <input type="text" name="judul" class="form-control" required placeholder="Contoh: Kegiatan Belajar">
                </div>
                
                <div>
                    <label>Deskripsi *</label>
                    <textarea name="deskripsi" class="form-control" required placeholder="Deskripsi singkat tentang item..."></textarea>
                </div>
                
                <div>
                    <label>Link YouTube (opsional)</label>
                    <input type="url" name="youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                    <small style="color: #666;">Masukkan URL video YouTube jika ada.</small>
                </div>

                <div>
                    <label>Upload Foto Thumbnail (opsional) (JPG, PNG, GIF max 5MB)</label>
                    <input type="file" name="file_foto" class="form-control" accept="image/*">
                    <small style="color: #666;">Boleh dikosongkan jika menggunakan thumbnail YouTube.</small>
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

    <!-- MODAL EDIT ITEM -->
    <div class="modal-bg" id="editModal">
        <div class="modal">
            <h3>✏️ Edit Item</h3>
            <form action="video-update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                
                <div>
                    <label>Judul *</label>
                    <input type="text" name="judul" id="edit_judul" class="form-control" required>
                </div>
                
                <div>
                    <label>Deskripsi *</label>
                    <textarea name="deskripsi" id="edit_deskripsi" class="form-control" required></textarea>
                </div>
                
                <div>
                    <label>Preview Thumbnail:</label>
                    <img id="edit_preview" src="https://via.placeholder.com/600x300?text=Preview" style="width: 100%; max-height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">
                </div>
                
                <div>
                    <label>Ganti Foto (Opsional)</label>
                    <input type="file" name="file_foto" class="form-control" accept="image/*">
                    <small style="color: #666;">Kosongkan jika tidak ingin mengganti foto</small>
                </div>

                <div>
                    <label>Link YouTube (opsional)</label>
                    <input type="url" name="youtube_url" id="edit_youtube_url" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
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

        // When user clicks Edit button -> read data-video and populate form
        function handleEditBtn(btn) {
            const data = btn.getAttribute('data-video');
            if (!data) return;
            try {
                const video = JSON.parse(data);
                document.getElementById('edit_id').value = video.id || '';
                document.getElementById('edit_judul').value = video.judul || '';
                document.getElementById('edit_deskripsi').value = video.deskripsi || '';
                document.getElementById('edit_youtube_url').value = video.youtube_url || '';
                // preview: use file_foto if exists else youtube thumbnail path (server side generated)
                let previewSrc = 'https://via.placeholder.com/600x300?text=Preview';
                if (video.file_foto && video.file_foto.trim() !== '') {
                    previewSrc = 'uploads/' + video.file_foto;
                } else if (video.youtube_url && video.youtube_url.trim() !== '') {
                    // try to generate youtube thumbnail client-side (basic)
                    const ytId = (function(url){
                        if(!url) return '';
                        // simple regex
                        const m = url.match(/(?:youtube\.com\/watch\?v=|youtube\.com\/.*[?&]v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/);
                        return m ? m[1] : '';
                    })(video.youtube_url);
                    if (ytId) previewSrc = 'https://img.youtube.com/vi/' + ytId + '/maxresdefault.jpg';
                }
                document.getElementById('edit_preview').src = previewSrc;
                openModal('editModal');
            } catch (e) {
                console.error('Invalid video data', e);
                alert('Gagal membuka editor. Silakan coba lagi.');
            }
        }

        // Delete button handler (uses data attributes)
        function handleDeleteBtn(btn) {
            const id = btn.getAttribute('data-id');
            const title = btn.getAttribute('data-title') || '';
            if (!id) return;
            if (confirm('Apakah Anda yakin ingin menghapus item "' + title + '"?\n\nTindakan ini tidak dapat dibatalkan!')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'video-delete.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = id;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Simple preview open (example: buka halaman publik atau modal preview — disesuaikan)
        function openPreview(id) {
            // contoh: buka halaman publik item
            if (!id) return;
            window.open('../video.php?id=' + encodeURIComponent(id), '_blank');
        }

        // Auto hide alert after 3 seconds (jika ada)
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
        
        /* video Card Improvements */
        .video-card {
            position: relative;
        }
        .video-card img {
            cursor: pointer;
            transition: transform 0.3s;
            border-radius: 8px;
        }
        .video-card:hover img {
            transform: scale(1.03);
        }
        
        /* Responsive images */
        @media (max-width: 768px) {
            .video-card img {
                height: 180px;
            }
        }
        
        @media (max-width: 600px) {
            .video-card img {
                height: 150px;
            }
        }
    </style>
</body>
</html>
