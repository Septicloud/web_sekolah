<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $youtube_url = mysqli_real_escape_string($koneksi, trim($_POST['youtube_url']));
    
    // Validate input
    if (empty($judul) || empty($deskripsi) || empty($youtube_url)) {
        header("Location: galeri-video.php?error=empty_field");
        exit;
    }
    
    // Validate YouTube URL (support berbagai format)
    $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/).+$/';
    if (!preg_match($pattern, $youtube_url)) {
        header("Location: galeri-video.php?error=invalid_url");
        exit;
    }
    
    // Get user ID from session (jika ada tabel user)
    $created_by = 1; // Default admin, atau bisa ambil dari session
    // $created_by = $_SESSION['user_id']; // jika ada
    
    // Insert to database
    $sql = "INSERT INTO galeri_video (judul, deskripsi, youtube_url, created_by) 
            VALUES ('$judul', '$deskripsi', '$youtube_url', $created_by)";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location:video.php.php?success=added");
    } else {
        error_log("MySQL Error: " . mysqli_error($koneksi));
        header("Location: video.php?error=failed");
    }
} else {
    header("Location: video.php");
}
?>