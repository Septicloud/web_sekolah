<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get message ID
if (!isset($_GET['id'])) {
    header("Location: kontak.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Handle form submission (update status & reply)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    $balasan = mysqli_real_escape_string($koneksi, trim($_POST['balasan']));
    
    if (in_array($status, ['baru', 'dibaca', 'dibalas'])) {
        $update_date = $status == 'dibaca' ? ", tanggal_dibaca=NOW()" : "";
        
        $sql = "UPDATE kontak SET 
                status='$status' 
                $update_date 
                WHERE id='$id'";
        
        if (mysqli_query($koneksi, $sql)) {
            // If there's a reply, you can store it or send email here
            if (!empty($balasan) && $status == 'dibalas') {
                // You can add email sending logic here
                // mail($email, "Balasan dari SLB Roza", $balasan);
            }
            
            $success = "Status pesan berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui status.";
        }
    }
}

// Mark as read when opening detail page
mysqli_query($koneksi, "UPDATE kontak SET status='dibaca', tanggal_dibaca=NOW() WHERE id='$id' AND status='baru'");

// Get message detail
$query = "SELECT * FROM kontak WHERE id='$id'";
$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: kontak.php");
    exit;
}

$pesan = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesan - SLB Roza</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="btn-menu" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h1>Detail Pesan</h1>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            <div class="detail-container">
                <!-- Alerts -->
                <?php if (isset($success)): ?>
                    <div class="alert-message alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert-message alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Message Detail -->
                <div class="message-detail">
                    <div class="message-header">
                        <div class="message-sender">
                            <div class="sender-name">
                                <?php echo htmlspecialchars($pesan['nama']); ?>
                            </div>
                            <div class="sender-email">
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo htmlspecialchars($pesan['email']); ?>">
                                    <?php echo htmlspecialchars($pesan['email']); ?>
                                </a>
                            </div>
                            <div class="message-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo date('d F Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></span>
                                </div>
                                <?php if ($pesan['tanggal_dibaca']): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        <span>Dibaca: <?php echo date('d F Y, H:i', strtotime($pesan['tanggal_dibaca'])); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="message-status-badge">
                            <span class="badge badge-<?php 
                                echo $pesan['status'] == 'baru' ? 'warning' : 
                                    ($pesan['status'] == 'dibaca' ? 'info' : 'success'); 
                            ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                <?php echo ucfirst($pesan['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="message-content">
                        <h3>Pesan:</h3>
                        <div class="message-text">
                            <?php echo nl2br(htmlspecialchars($pesan['pesan'])); ?>
                        </div>
                    </div>

                    <div class="message-info">
                        <div class="info-row">
                            <span class="info-label">IP Address:</span>
                            <span class="info-value"><?php echo htmlspecialchars($pesan['ip_address'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">User Agent:</span>
                            <span class="info-value" style="word-break: break-all;">
                                <?php echo htmlspecialchars(substr($pesan['user_agent'] ?? 'N/A', 0, 50)); ?>...
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Diterima:</span>
                            <span class="info-value">
                                <?php echo date('d F Y, H:i:s', strtotime($pesan['tanggal_kirim'])); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="reply-section">
                    <h3>Kelola Pesan</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="baru" <?php echo $pesan['status'] == 'baru' ? 'selected' : ''; ?>>Baru</option>
                                <option value="dibaca" <?php echo $pesan['status'] == 'dibaca' ? 'selected' : ''; ?>>Dibaca</option>
                                <option value="dibalas" <?php echo $pesan['status'] == 'dibalas' ? 'selected' : ''; ?>>Dibalas</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="balasan">Balasan (Opsional)</label>
                            <textarea 
                                name="balasan" 
                                id="balasan" 
                                class="form-textarea" 
                                placeholder="Tulis balasan Anda di sini... (akan dikirim via email)"
                            ></textarea>
                            <small style="color: var(--gray); font-size: 0.875rem;">
                                * Jika status diubah ke "Dibalas", Anda dapat menuliskan balasan di sini.
                            </small>
                        </div>

                        <div class="form-actions">
                            <a href="kontak.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-info">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="js/admin.js"></script>
    <script>
        // Auto-hide alerts
        const alerts = document.querySelectorAll('.alert-message');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Confirm before leaving if form has changes
        let formChanged = false;
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('select, textarea');
        
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', (e) => {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        form.addEventListener('submit', () => {
            formChanged = false;
        });

        // Character counter for textarea
        const textarea = document.getElementById('balasan');
        const counter = document.createElement('div');
        counter.style.cssText = 'text-align: right; font-size: 0.875rem; color: var(--gray); margin-top: 0.5rem;';
        textarea.parentNode.insertBefore(counter, textarea.nextSibling);

        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = `${length} karakter`;
        });

        // Status change warning
        const statusSelect = document.getElementById('status');
        statusSelect.addEventListener('change', function() {
            if (this.value === 'dibalas') {
                alert('Pastikan Anda telah menulis balasan sebelum menyimpan dengan status "Dibalas".');
            }
        });
    </script>
</body>
</html>