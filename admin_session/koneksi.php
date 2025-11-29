<?php
$host = "localhost";
$user = "root";     // sesuaikan dengan username MySQL kamu
$pass = "";          // isi password MySQL kamu kalau ada
$db   = "db_slb_roza";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
?>
