-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Nov 2025 pada 02.18
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_slb_roza`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `eskul`
--

CREATE TABLE `eskul` (
  `id` int(11) NOT NULL,
  `nama_eskul` varchar(125) NOT NULL,
  `file_foto` varchar(125) NOT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `eskul`
--

INSERT INTO `eskul` (`id`, `nama_eskul`, `file_foto`, `deskripsi`, `created_at`) VALUES
(6, 'Tennis Meja', '1764253871_692860afe44d4.jpg', 'lormeajshuahd', '2025-11-27 14:31:28'),
(8, 'Angklung', '1764254947_692864e33fc8b.jpg', 'msc', '2025-11-27 14:49:07'),
(9, 'Menggambar', '1764254985_69286509e72ab.jpg', 'jjhas', '2025-11-27 14:49:45'),
(10, 'nasyid', '1764255011_69286523ea6a5.jpg', 'adf', '2025-11-27 14:50:11'),
(11, 'Nyanyi', '1764255082_6928656a6bca2.jpg', 'kajdfj', '2025-11-27 14:51:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri_video`
--

CREATE TABLE `galeri_video` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `youtube_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `galeri_video`
--

INSERT INTO `galeri_video` (`id`, `judul`, `deskripsi`, `youtube_url`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'test', 'Deskripsi panjang', 'https://youtu.be/J1NKblzxl5U?si=yWy16HIlgra3rG03', '2025-11-14 01:40:07', '2025-11-14 02:51:07', 1),
(2, 'anak anak', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'https://youtu.be/1xBczuFIExE?si=rAIrnoHd6WQ3RhO0', '2025-11-14 02:50:11', '2025-11-14 03:41:29', 1),
(3, 'anak anak', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat.', 'https://youtu.be/1xBczuFIExE?si=rAIrnoHd6WQ3RhO0', '2025-11-14 02:50:30', '2025-11-14 03:41:09', 1),
(4, 'anak anak', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,\r\nquis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo\r\nconsequat. Duis aute irure dolor in reprehenderit in voluptate velit esse\r\ncillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non\r\nproident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'https://youtu.be/J1NKblzxl5U?si=yWy16HIlgra3rG03', '2025-11-14 02:50:55', '2025-11-14 03:41:23', 1),
(6, 'anak anak', 'hspi', 'https://youtu.be/1xBczuFIExE?si=rAIrnoHd6WQ3RhO0', '2025-11-14 03:54:09', '2025-11-14 03:54:09', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontak`
--

CREATE TABLE `kontak` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('baru','dibaca','dibalas') DEFAULT 'baru',
  `tanggal_kirim` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_dibaca` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontak`
--

INSERT INTO `kontak` (`id`, `nama`, `email`, `pesan`, `status`, `tanggal_kirim`, `tanggal_dibaca`, `ip_address`, `user_agent`) VALUES
(1, 'Mirza', 'septiawanhadi38@gmail.com', 'test', 'dibaca', '2025-11-13 04:31:52', '2025-11-14 01:09:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(2, 'daniel', 'danielhs8@upi.edu', 'cape euy', 'dibalas', '2025-11-14 01:10:18', '2025-11-14 01:10:25', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(3, 'daniel', 'danielhs8@upi.edu', 'cape euy', 'dibaca', '2025-11-14 01:11:55', '2025-11-14 01:45:54', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0'),
(4, 'mirza', 'mirzaary6@gmail.com', 'halo', 'dibaca', '2025-11-14 03:56:45', '2025-11-15 07:31:46', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidik`
--

CREATE TABLE `pendidik` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendidik`
--

INSERT INTO `pendidik` (`id`, `nama`, `jabatan`, `deskripsi`, `foto`, `created_at`) VALUES
(3, 'Mirza', 'Rektor', 'akdgkad', '1764292561_6928f7d11d736.jpeg', '2025-11-28 01:15:07'),
(4, 'Daniel', 'Wakil Rektor', 'jkafh', '1764292615_6928f80751b62.jpeg', '2025-11-28 01:16:55');

-- --------------------------------------------------------

--
-- Struktur dari tabel `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `nama_foto` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_foto` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `photos`
--

INSERT INTO `photos` (`id`, `nama_foto`, `deskripsi`, `file_foto`, `created_at`) VALUES
(19, 'Bioskop', 'kjjgad', '1764292057_6928f5d9d3245.jpg', '2025-11-28 00:59:54'),
(20, 'Berenang', 'akjs', '1764291982_6928f58e04b4f.jpg', '2025-11-28 01:06:22'),
(21, 'Kereta Api', 'siswa dan siswi berkunjung ke stasiun kereta api', '1764292238_6928f68ec4ba4.jpg', '2025-11-28 01:10:38'),
(22, 'bermain', 'siswa dan siswi sedang berlomba 17 agustusan', '1764292268_6928f6ace0dc9.jpg', '2025-11-28 01:11:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil`
--

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `bagian` varchar(50) NOT NULL,
  `isi` text NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id`, `bagian`, `isi`, `updated_at`) VALUES
(1, 'sejarah', 'SLB Roza berdiri sejak tahun 2029', '2025-10-23 17:46:36'),
(2, 'visi', 'Terwujudnya murid yang religius,berprestasi, dan mandiri di tahun 2030', '2025-11-27 21:46:54'),
(3, 'misi', '1. Mengembangkan perilaku akhlakul karimah\r\n2. Mengembangkan prestasi di bidang keagamaan\r\n3. Mengembangkan prestasi di bidang olahraga\r\n4. Mengembangkan prestasi di bidang seni\r\n5. Mengembangkan prestasi di bidang akademik\r\n6. Mengembangkan prestasi di bidang finansial', '2025-11-27 21:46:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sarana_prasarana`
--

CREATE TABLE `sarana_prasarana` (
  `id` int(11) NOT NULL,
  `nama_sarana` varchar(125) NOT NULL,
  `file_foto` varchar(125) NOT NULL,
  `deskripsi` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sarana_prasarana`
--

INSERT INTO `sarana_prasarana` (`id`, `nama_sarana`, `file_foto`, `deskripsi`, `created_at`) VALUES
(14, 'Ruang Kelas', '1764254309_6928626545093.jpg', 'JAdaudguag', '2025-11-27 14:38:29'),
(15, 'Ruang Guru', '1764254346_6928628a4aa3c.jpg', 'ajdhjadg', '2025-11-27 14:39:06'),
(16, 'Toilet Atas', '1764254434_692862e25b00f.jpg', 'dilantai atas ada toilet dan dilengkapi wastafel', '2025-11-27 14:40:34'),
(17, 'Toilet Bawah', '1764254469_69286305e8101.jpg', 'dilantai bawah ada 2 toilet siswa dan siswi', '2025-11-27 14:41:09'),
(18, 'Ruang Kelas', '1764254575_6928636fb97da.jpg', 'ajksadj', '2025-11-27 14:42:55'),
(19, 'Ruang Kepala Sekolah', '1764255381_69286695eb27f.jpg', 'kagfkaf', '2025-11-27 14:56:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `username` varchar(12) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`username`, `email`, `password`) VALUES
('admin', NULL, '123'),
('a', 'a@gmail.com', 'h'),
('septiawan', 'septiawanhadi38@gmail.com', '123456'),
('admin1', 'admin1@gmail.com', '1234');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `eskul`
--
ALTER TABLE `eskul`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `galeri_video`
--
ALTER TABLE `galeri_video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indeks untuk tabel `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pendidik`
--
ALTER TABLE `pendidik`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `sarana_prasarana`
--
ALTER TABLE `sarana_prasarana`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `eskul`
--
ALTER TABLE `eskul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `galeri_video`
--
ALTER TABLE `galeri_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pendidik`
--
ALTER TABLE `pendidik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sarana_prasarana`
--
ALTER TABLE `sarana_prasarana`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
