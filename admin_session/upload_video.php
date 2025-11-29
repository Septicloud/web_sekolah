<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_video = mysqli_real_escape_string($koneksi, $_POST['nama_video']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Validasi file
    if (!isset($_FILES['file_video']) || $_FILES['file_video']['error'] !== UPLOAD_ERR_OK) {
        header("Location: video.php?error=upload_failed");
        exit;
    }
    
    $file = $_FILES['file_video'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validasi tipe file
    $allowed_types = array('video/mp4', 'video/mpeg', 'video/quicktime');
    if (!in_array($file_type, $allowed_types)) {
        header("Location: video.php?error=invalid_file");
        exit;
    }
    
    // Validasi ukuran file (max 50MB)
    $max_size = 50 * 1024 * 1024; // 50MB in bytes
    if ($file_size > $max_size) {
        header("Location: video.php?error=invalid_file");
        exit;
    }
    
    // Generate nama file unik
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
    
    // Folder upload
    $upload_dir = 'uploads/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $target_file = $upload_dir . $new_file_name;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Simpan ke database
        $stmt = mysqli_prepare($koneksi, "INSERT INTO videos (nama_video, deskripsi, file_video) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama_video, $deskripsi, $new_file_name);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: video.php?success=added");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            // Hapus file jika gagal insert ke database
            unlink($target_file);
            header("Location: video.php?error=upload_failed");
            exit;
        }
    } else {
        header("Location: video.php?error=upload_failed");
        exit;
    }
} else {
    header("Location: video.php");
    exit;
}
?>