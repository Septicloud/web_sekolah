<?php
/**
 * Script untuk Migrasi Password ke Hash
 * Jalankan script ini SEKALI untuk mengubah semua password yang belum di-hash
 * 
 * PENTING: Jalankan di browser atau command line setelah backup database
 * Akses: http://yoursite.com/admin_session/migrate_passwords.php
 */

include 'koneksi.php';

// Cek apakah user sudah login atau ada parameter khusus
$allow_migration = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$allow_migration) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Migrasi Password</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }
            .container {
                max-width: 500px;
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            }
            .alert {
                border-radius: 8px;
                border-left: 4px solid;
            }
            .btn {
                border-radius: 8px;
                padding: 12px 24px;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2 class="mb-4" style="font-weight: 700; color: #363636;">
                <i class="bi bi-shield-lock"></i> Migrasi Password
            </h2>
            
            <div class="alert alert-warning" style="border-color: #f59e0b; background: #fef3c7; color: #92400e;">
                <strong>Peringatan!</strong> Script ini akan mengubah semua password di database menjadi format hash yang aman.
            </div>

            <div class="alert alert-info" style="border-color: #3b82f6; background: #dbeafe; color: #1e40af;">
                <strong>Instruksi:</strong>
                <ol style="margin-bottom: 0; padding-left: 20px;">
                    <li>Backup database Anda terlebih dahulu</li>
                    <li>Klik tombol "Mulai Migrasi" di bawah</li>
                    <li>Tunggu hingga proses selesai</li>
                    <li>Hapus file ini setelah selesai</li>
                </ol>
            </div>

            <form method="GET" action="">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="btn btn-primary" style="width: 100%; background: #0d6efd; color: white; border: none;">
                    <i class="bi bi-play-fill"></i> Mulai Migrasi
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 0.9rem;">
                Atau <a href="index.php" style="color: #0d6efd; text-decoration: none; font-weight: 600;">kembali ke dashboard</a>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Proses migrasi
$result = [];
$errors = [];

try {
    // Ambil semua user
    $query = "SELECT id, username, password FROM user";
    $result_query = mysqli_query($koneksi, $query);

    if (!$result_query) {
        throw new Exception("Error query: " . mysqli_error($koneksi));
    }

    $updated = 0;
    $skipped = 0;

    while ($row = mysqli_fetch_assoc($result_query)) {
        $id = $row['id'];
        $password = $row['password'];

        // Cek apakah password sudah di-hash (hash biasanya dimulai dengan $2y$)
        if (strpos($password, '$2y$') === 0 || strpos($password, '$2a$') === 0 || strpos($password, '$2b$') === 0) {
            $skipped++;
            continue;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update database
        $update_query = "UPDATE user SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $update_query);
        
        if (!$stmt) {
            throw new Exception("Error prepare: " . mysqli_error($koneksi));
        }

        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error execute: " . mysqli_stmt_error($stmt));
        }

        $updated++;
        mysqli_stmt_close($stmt);
    }

    $success = true;
    $message = "Migrasi berhasil! $updated password telah di-hash, $skipped password sudah di-hash sebelumnya.";

} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Migrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .container {
            max-width: 500px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .alert {
            border-radius: 8px;
            border-left: 4px solid;
        }
        .btn {
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4" style="font-weight: 700; color: #363636;">
            <i class="bi bi-check-circle"></i> Hasil Migrasi
        </h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success" style="border-color: #10b981; background: #d1fae5; color: #065f46;">
                <strong>Sukses!</strong> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" style="border-color: #ef4444; background: #fee2e2; color: #991b1b;">
                <strong>Gagal!</strong> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <p style="margin-top: 20px; color: #6b7280; line-height: 1.6;">
            Migrasi password telah selesai. Semua password di database sekarang menggunakan format hash yang aman. 
            <strong>Jangan lupa untuk menghapus file ini (migrate_passwords.php) untuk alasan keamanan.</strong>
        </p>

        <a href="index.php" class="btn btn-primary" style="width: 100%; background: #0d6efd; color: white; border: none; text-decoration: none; display: inline-block; text-align: center;">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
