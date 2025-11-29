<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_foto = mysqli_real_escape_string($koneksi, $_POST['nama_foto']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Validasi file
    if (!isset($_FILES['file_foto']) || $_FILES['file_foto']['error'] !== UPLOAD_ERR_OK) {
        header("Location: gallery.php?error=upload_failed");
        exit;
    }
    
    $file = $_FILES['file_foto'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validasi tipe file
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    if (!in_array($file_type, $allowed_types)) {
        header("Location: gallery.php?error=invalid_file");
        exit;
    }
    
    // Validasi ukuran file (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB in bytes
    if ($file_size > $max_size) {
        header("Location: gallery.php?error=invalid_file");
        exit;
    }
    
    // Validasi ekstensi file
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        header("Location: gallery.php?error=invalid_file");
        exit;
    }
    
    // Generate nama file unik
    $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
    
    // Folder upload
    $upload_dir = '../uploads/galeri/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $target_file = $upload_dir . $new_file_name;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Resize image jika terlalu besar (opsional)
        // resizeImage($target_file, $target_file, 1200, 1200);
        
        // Simpan ke database
        $stmt = mysqli_prepare($koneksi, "INSERT INTO photos (nama_foto, deskripsi, file_foto) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama_foto, $deskripsi, $new_file_name);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: gallery.php?success=added");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            // Hapus file jika gagal insert ke database
            unlink($target_file);
            header("Location: gallery.php?error=upload_failed");
            exit;
        }
    } else {
        header("Location: gallery.php?error=upload_failed");
        exit;
    }
} else {
    header("Location: gallery.php");
    exit;
}

// Fungsi resize image (opsional)
function resizeImage($source, $destination, $max_width, $max_height) {
    list($width, $height, $type) = getimagesize($source);
    
    if ($width <= $max_width && $height <= $max_height) {
        return true; // Tidak perlu resize
    }
    
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = round($width * $ratio);
    $new_height = round($height * $ratio);
    
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source);
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($new_image, $destination, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($new_image, $destination, 9);
            break;
        case IMAGETYPE_GIF:
            imagegif($new_image, $destination);
            break;
    }
    
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return true;
}
?>