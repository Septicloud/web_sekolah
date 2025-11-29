<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $deskripsi = mysqli_real_escape_string($koneksi, trim($_POST['deskripsi']));
    $youtube_url = mysqli_real_escape_string($koneksi, trim($_POST['youtube_url']));
    
    // Validate input
    if (empty($id) || empty($judul) || empty($deskripsi) || empty($youtube_url)) {
        header("Location: galeri-video.php?error=empty_field");
        exit;
    }
    
    // Validate YouTube URL
    $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/).+$/';
    if (!preg_match($pattern, $youtube_url)) {
        header("Location: galeri-video.php?error=invalid_url");
        exit;
    }
    
    // Check if video exists
    $check = mysqli_query($koneksi, "SELECT id FROM galeri_video WHERE id='$id'");
    if (mysqli_num_rows($check) == 0) {
        header("Location: galeri-video.php?error=not_found");
        exit;
    }
    
    // Update database
    $sql = "UPDATE galeri_video SET 
            judul='$judul', 
            deskripsi='$deskripsi', 
            youtube_url='$youtube_url',
            updated_at=CURRENT_TIMESTAMP
            WHERE id='$id'";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: video.php?success=updated");
    } else {
        error_log("MySQL Error: " . mysqli_error($koneksi));
        header("Location: video.php?error=failed");
    }
} else {
    header("Location: video.php");
}
?>
