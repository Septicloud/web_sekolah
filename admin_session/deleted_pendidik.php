<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Ambil data pendidik dari database
    $stmt = mysqli_prepare($koneksi, "SELECT foto FROM pendidik WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pendidik = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($pendidik) {
        $file_path = 'uploads/' . $pendidik['foto'];
        
        // Hapus dari database
        $stmt = mysqli_prepare($koneksi, "DELETE FROM pendidik WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            
            // Hapus file fisik jika ada
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            header("Location: pendidik_admin.php?success=deleted");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            header("Location: pendidik_admin.php?error=delete_failed");
            exit;
        }
    } else {
        header("Location: pendidik_admin.php?error=delete_failed");
        exit;
    }
} else {
    header("Location: pendidik_admin.php");
    exit;
}
?>