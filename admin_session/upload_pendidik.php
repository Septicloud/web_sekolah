<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Validasi file
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: pendidik_admin.php?error=upload_failed");
        exit;
    }
    
    $file = $_FILES['foto'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validasi tipe file
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
    if (!in_array($file_type, $allowed_types)) {
        header("Location: pendidik_admin.php?error=invalid_file");
        exit;
    }
    
    // Validasi ukuran file (max 2MB)
    $max_size = 2 * 1024 * 1024; // 2MB in bytes
    if ($file_size > $max_size) {
        header("Location: pendidik_admin.php?error=invalid_file");
        exit;
    }
    
    // Validasi ekstensi file
    $allowed_extensions = array('jpg', 'jpeg', 'png');
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        header("Location: pendidik_admin.php?error=invalid_file");
        exit;
    }
    
    // Generate nama file unik
    $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
    
    // Folder upload
    $upload_dir = '../uploads/pendidik/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $target_file = $upload_dir . $new_file_name;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Simpan ke database
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pendidik (nama, jabatan, deskripsi, foto) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $jabatan, $deskripsi, $new_file_name);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: pendidik_admin.php?success=added");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            // Hapus file jika gagal insert ke database
            unlink($target_file);
            header("Location: pendidik_admin.php?error=upload_failed");
            exit;
        }
    } else {
        header("Location: pendidik_admin.php?error=upload_failed");
        exit;
    }
} else {
    header("Location: pendidik_admin.php");
    exit;
}
?>