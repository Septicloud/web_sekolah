<?php
include 'koneksi.php';
session_start();

// Jika sudah login, lempar ke index
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = $_POST['password'];

    // Cek username
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // --- LOGIKA SMART LOGIN ---
        
        // Skenario 1: Password di database SUDAH di-hash (Aman)
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            // $_SESSION['user_id'] = $row['id']; // Aktifkan jika tabel user punya kolom id
            
            echo "<script>
                    alert('Login Berhasil!');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }
        // Skenario 2: Password di database MASIH teks biasa (Misal: "123")
        // Fitur ini akan mengizinkan login DAN otomatis mengamankan password di database
        elseif ($row['password'] === $password) {
            $_SESSION['username'] = $row['username'];
            
            // Auto-Fix: Update password di database menjadi hash agar aman untuk login berikutnya
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Kita update berdasarkan username karena mungkin Anda belum punya kolom ID yang konsisten
            $update_stmt = mysqli_prepare($koneksi, "UPDATE user SET password = ? WHERE username = ?");
            mysqli_stmt_bind_param($update_stmt, "ss", $new_hash, $username);
            mysqli_stmt_execute($update_stmt);
            
            echo "<script>
                    alert('Login Berhasil! Password Anda telah diamankan otomatis.');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        } 
        else {
            $error_message = 'Password salah!';
        }
    } else {
        $error_message = 'Username tidak ditemukan!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Admin - SLB Roza</title>
  
  <link rel="stylesheet" href="../CSS/login.css"> 
  <style>
    /* Perbaikan kecil untuk layout input group */
    .password-group {
        position: relative;
        width: 100%;
    }
    .toggle-pass {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .toggle-pass img {
        width: 20px; 
        height: 20px; 
        opacity: 0.6;
        transition: opacity 0.2s;
    }
    .toggle-pass:hover img {
        opacity: 1;
    }
    /* Style Alert Error */
    .alert-box {
        background-color: #fee2e2;
        border: 1px solid #ef4444;
        color: #b91c1c;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="brand">
      <img src="../assets/img/logosekolah.png" alt="Logo SLB Roza" onerror="this.style.display='none'">
      <h1 id="loginTitle">Panel Admin</h1>
      <p>Masuk ke akun Anda</p>
    </div>

    <?php if ($error_message): ?>
      <div class="alert-box">
        <span>⚠️</span> <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      
      <div class="field-row">
        <label for="username">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username" 
          autocomplete="username" 
          required 
          placeholder="Masukkan username"
          value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
        />
      </div>

      <div class="field-row">
        <label for="password">Password</label>
        <div class="password-group">
            <input 
              type="password" 
              id="password" 
              name="password" 
              autocomplete="current-password" 
              required 
              placeholder="Masukkan password"
              style="padding-right: 45px;" 
            />
            <button type="button" class="toggle-pass" id="toggleBtn" title="Lihat Password">
              <img src="../assets/img/icons/visible.png" id="passIcon" alt="👁️">
            </button>
        </div>
      </div>

      <div class="row-between">
        <label class="checkbox">
            <input type="checkbox" id="remember" name="remember" /> 
            <span>Ingat saya</span>
        </label>
        <a href="lupa_password.php" class="secondary">Lupa password?</a>
      </div>

      <button class="btn" type="submit">Masuk</button>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: #888;">
        &copy; <?php echo date('Y'); ?> SLB Roza
    </div>
  </div>

  <script>
    // LOGIKA TOMBOL MATA (SHOW/HIDE PASSWORD)
    const toggleBtn = document.getElementById('toggleBtn');
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('passIcon');

    toggleBtn.addEventListener('click', function() {
      // Cek tipe saat ini
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Ganti Icon (Pastikan file gambar ada di folder ../assets/img/icons/)
      // Jika tidak ada gambar, teks alt akan muncul, tapi fungsi tetap jalan.
      if (type === 'text') {
          icon.src = '../assets/img/icons/hide.png';
      } else {
          icon.src = '../assets/img/icons/visible.png';
      }
    });
  </script>

</body>
</html>