<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    
    // Check if video exists
    $check = mysqli_query($koneksi, "SELECT id FROM galeri_video WHERE id='$id'");
    if (mysqli_num_rows($check) == 0) {
        header("Location: galeri-video.php?error=not_found");
        exit;
    }
    
    // Delete from database
    $sql = "DELETE FROM galeri_video WHERE id='$id'";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: video.php?success=deleted");
    } else {
        error_log("MySQL Error: " . mysqli_error($koneksi));
        header("Location: video.php?error=failed");
    }
} else {
    header("Location: video.php");
}
?>