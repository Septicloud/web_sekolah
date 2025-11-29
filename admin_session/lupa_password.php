<?php
include 'koneksi.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi Input
    if (empty($username) || empty($email) || empty($new_password)) {
        $error = 'Harap isi semua kolom!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } elseif (strlen($new_password) > 12) {
        // Validasi tambahan karena database cuma muat 12 karakter
        $error = 'Password maksimal 12 karakter (Keterbatasan Database).';
    } else {
        // 2. Cek user berdasarkan Username & Email (Tanpa select ID)
        $stmt = mysqli_prepare($koneksi, "SELECT username FROM user WHERE username = ? AND email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            // User ditemukan.
            // Karena database varchar(12), kita TIDAK BISA pakai password_hash().
            // Kita simpan password apa adanya (Plain Text).
            
            $stmt_update = mysqli_prepare($koneksi, "UPDATE user SET password = ? WHERE username = ?");
            mysqli_stmt_bind_param($stmt_update, "ss", $new_password, $username);
            
            if (mysqli_stmt_execute($stmt_update)) {
                echo "<script>
                        alert('Password berhasil diubah! Silakan login.');
                        window.location.href = 'login.php';
                      </script>";
                exit;
            } else {
                $error = 'Gagal mengubah password.';
            }
            mysqli_stmt_close($stmt_update);
        } else {
            $error = 'Username atau Email tidak ditemukan!';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Admin SLB Roza</title>
    <link rel="stylesheet" href="../CSS/login.css"> 
    <link rel="icon" href="../assets/img/logosekolah.png" type="image/png">
</head>
<body>
    <main class="container">
        <div class="brand">
            <img src="../assets/img/logosekolah.png" alt="Logo SLB Roza">
            <h1 id="loginTitle">Lupa Password</h1>
            <p>Verifikasi akun Anda untuk membuat password baru</p>
        </div>

        <?php if ($error): ?>
        <div class="alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="field-row">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username Anda" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="field-row">
                <label for="email">Email Terdaftar</label>
                <input type="email" id="email" name="email" required placeholder="Masukkan email terdaftar" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">

            <div class="field-row">
                <label for="new_password">Password Baru (Max 12 Karakter)</label>
                <input type="password" id="new_password" name="new_password" required placeholder="Password baru (max 12 huruf)" maxlength="12">
            </div>

            <div class="field-row">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Ulangi password baru" maxlength="12">
            </div>

            <div style="margin-top: 1rem;">
                <button class="btn" type="submit">Reset Password</button>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="login.php" class="secondary">Kembali ke Login</a>
            </div>
        </form>
    </main>
</body>
</html>