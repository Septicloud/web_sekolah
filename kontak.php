<?php
include 'admin_session/koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $pesan = mysqli_real_escape_string($koneksi, trim($_POST['pesan']));
    
    // Validasi
    if (empty($nama) || empty($email) || empty($pesan)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Get IP address and user agent
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = mysqli_real_escape_string($koneksi, $_SERVER['HTTP_USER_AGENT']);
        
        // Insert to database
        $sql = "INSERT INTO kontak (nama, email, pesan, ip_address, user_agent, status) 
                VALUES ('$nama', '$email', '$pesan', '$ip_address', '$user_agent', 'baru')";
        
        if (mysqli_query($koneksi, $sql)) {
            $success = 'Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.';
            // Reset form
            $_POST = array();
        } else {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Hubungi SLB Roza untuk informasi lebih lanjut tentang pendidikan anak berkebutuhan khusus">
    <title>Hubungi Kami - SLB Roza</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="CSS/style.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <?php include 'partials/header.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <h1>Hubungi Kami</h1>
        <p>Jika Anda memiliki pertanyaan atau ingin informasi lebih lanjut, silakan hubungi kami melalui formulir di bawah ini atau kunjungi lokasi kami.</p>
    </div>
    <br>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Contact Grid -->
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info">
                <h2>Informasi Kontak</h2>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Alamat</h3>
                        <p>Soreang</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Telepon</h3>
                            <p><a href="tel:081234567890">
                                0812-3456-7890
                        </a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p><a href="mailto:info@slbroza.sch.id">
                            info@slbroza.sch.id
                        </a></p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Jam Operasional</h3>
                        <p>Senin - Jumat: 07:00 - 15:00<br>
                           Sabtu: 07:00 - 12:00<br>
                           Minggu: Tutup</p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>Kirim Pesan</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="contactForm">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span>*</span></label>
                        <input 
                            type="text" 
                            id="nama" 
                            name="nama" 
                            class="form-control" 
                            placeholder="Masukkan nama lengkap Anda"
                            value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span>*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="contoh@email.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="pesan">Pesan <span>*</span></label>
                        <textarea 
                            id="pesan" 
                            name="pesan" 
                            class="form-control" 
                            placeholder="Tuliskan pesan Anda di sini..."
                            required><?php echo isset($_POST['pesan']) ? htmlspecialchars($_POST['pesan']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-section mt-4">
            <h2>Lokasi Kami</h2>
            <div class="map-wrapper">
                <!-- Ganti dengan Google Maps embed URL sesuai lokasi sekolah -->
                <iframe 
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.459020268253!2d107.5419422!3d-7.019092000000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68ed42b4b4bf61%3A0xe820127a4166a0ca!2sSLB%20ROUDHOTUL%20JANNAH!5e0!3m2!1sid!2sid!4v1732683960000!5m2!1sid!2sid" 
    width="100%" 
    height="450" 
    style="border:0;" 
    allowfullscreen="" 
    loading="lazy" 
    referrerpolicy="no-referrer-when-downgrade">
</iframe>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Form validation and submission
        const form = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Reset form if submission was successful
        <?php if ($success): ?>
            setTimeout(() => {
                document.getElementById('contactForm').reset();
            }, 100);
        <?php endif; ?>

        // Input animations
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('label').style.color = '#0d6efd';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.querySelector('label').style.color = '#333';
            });
        });

        // Character counter for textarea
        const textarea = document.getElementById('pesan');
        const counter = document.createElement('div');
        counter.style.cssText = 'text-align: right; font-size: 0.875rem; color: #666; margin-top: 0.5rem;';
        textarea.parentNode.appendChild(counter);

        textarea.addEventListener('input', function() {
            const length = this.value.length;
            counter.textContent = `${length} karakter`;
            counter.style.color = length > 500 ? '#dc3545' : '#666';
        });

        // Active navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === 'kontak.php') {
                link.classList.add('active');
            }
        });
    </script>
</body>
</html>