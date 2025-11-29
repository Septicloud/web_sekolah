<?php
// delete.php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  //ambil nama file
  $stmt = mysqli_prepare($koneksi, "SELECT file_foto FROM eskul WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $file);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  if ($file) {
    $path = 'uploads/' . $file;
    if (file_exists($path)) unlink($path);
  }

  $stmt = mysqli_prepare($koneksi, "DELETE FROM eskul WHERE id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  header("Location: eskul.php");
  exit;
}
header("Location: eskul.php");