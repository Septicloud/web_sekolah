<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $nama_eskul = mysqli_real_escape_string($koneksi, $_POST['nama_eskul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Cek apakah ada file foto baru yang diupload
    if (!empty($_FILES['file_foto']['name'])) {
        $file = $_FILES['file_foto'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = $file['type'];
        
        // Validasi tipe file
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        if (!in_array($file_type, $allowed_types)) {
            header("Location: eskul.php?error=invalid_file");
            exit;
        }
        
        // Validasi ukuran file (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file_size > $max_size) {
            header("Location: eskul.php?error=invalid_file");
            exit;
        }
        
        // Validasi ekstensi file
        $allowed_extensions = array('jpg', 'jpeg', 'png');
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_extensions)) {
            header("Location: eskul.php?error=invalid_file");
            exit;
        }
        
        // Generate nama file unik
        $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
        
        // Folder upload
        $upload_dir = 'uploads/eskul/';
        $target_file = $upload_dir . $new_file_name;
        
        // Ambil nama file lama dari database
        $stmt = mysqli_prepare($koneksi, "SELECT file_foto FROM eskul WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $old_data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        // Upload file baru
        if (move_uploaded_file($file_tmp, $target_file)) {
            // Hapus file lama jika ada
            if ($old_data && file_exists($upload_dir . $old_data['file_foto'])) {
                unlink($upload_dir . $old_data['file_foto']);
            }
            
            // Update database dengan file baru
            $stmt = mysqli_prepare($koneksi, "UPDATE eskul SET nama_eskul = ?, deskripsi = ?, file_foto = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $nama_eskul, $deskripsi, $new_file_name, $id);
        } else {
            header("Location: eskul.php?error=update_failed");
            exit;
        }
    } else {
        // Update tanpa mengubah file foto
        $stmt = mysqli_prepare($koneksi, "UPDATE eskul SET nama_eskul = ?, deskripsi = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $nama_eskul, $deskripsi, $id);
    }
    
    // Eksekusi query update
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: eskul.php?success=updated");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        header("Location: eskul.php?error=update_failed");
        exit;
    }
} else {
    header("Location: eskul.php");
    exit;
}
?>