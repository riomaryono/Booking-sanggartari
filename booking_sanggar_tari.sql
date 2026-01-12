-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 12 Jan 2026 pada 17.29
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booking_sanggar_tari`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `kategori_alamat` text,
  `kategori_daerah` varchar(100) DEFAULT NULL,
  `id_tarian` int NOT NULL,
  `jenis_acara` varchar(50) DEFAULT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `total_harga` int DEFAULT NULL,
  `durasi_jam` int DEFAULT '1',
  `jumlah_penari` int DEFAULT '1',
  `status` enum('pending','diterima','ditolak') DEFAULT 'pending',
  `catatan` text,
  `alasan_tolak` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_bayar` enum('belum','menunggu verifikasi','lunas') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `kategori_alamat`, `kategori_daerah`, `id_tarian`, `jenis_acara`, `tanggal_booking`, `jam_mulai`, `total_harga`, `durasi_jam`, `jumlah_penari`, `status`, `catatan`, `alasan_tolak`, `created_at`, `bukti_pembayaran`, `status_bayar`) VALUES
(6, 8, 'asd', 'Jakarta', 1, 'Pernikahan', '2026-01-13', '09:35:00', 650000, 1, 1, 'ditolak', NULL, NULL, '2026-01-12 12:36:41', NULL, 'belum'),
(7, 8, 'adfsafa', 'Bogor', 1, 'Pernikahan', '2026-01-13', '09:44:00', 750000, 1, 1, 'ditolak', NULL, NULL, '2026-01-12 12:42:09', NULL, 'belum'),
(8, 8, 'kemang', 'Jakarta', 3, 'Pernikahan', '2026-01-13', '10:25:00', 850000, 1, 2, 'ditolak', NULL, 'full', '2026-01-12 13:24:03', NULL, 'belum'),
(9, 8, 'sumatera', 'Luar Jabodetabek', 4, 'Pernikahan', '2026-01-13', '10:38:00', 1175000, 1, 3, 'diterima', NULL, '', '2026-01-12 13:36:14', 'BUKTI_9_1768226996.png', 'lunas'),
(10, 10, 'UIN', 'Depok', 4, 'Wisuda', '2026-01-20', '10:38:00', 1175000, 1, 7, 'diterima', NULL, '', '2026-01-12 14:38:05', 'BUKTI_10_1768229069.png', 'lunas'),
(11, 10, 'parung', 'Bogor', 3, 'Pernikahan', '2026-01-13', '00:54:00', 1550000, 1, 6, 'diterima', NULL, '', '2026-01-12 15:52:34', 'BUKTI_11_1768233254.png', 'lunas'),
(12, 10, 'kemang', 'Jakarta', 1, 'Pernikahan', '2026-01-14', '14:04:00', 1150000, 1, 6, 'ditolak', NULL, 'full', '2026-01-12 17:01:11', NULL, 'belum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customer`
--

CREATE TABLE `customer` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id` int NOT NULL,
  `judul` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id`, `judul`, `foto`, `created_at`) VALUES
(1, 'tari jaipong', '6964bfd13bc29.png', '2026-01-12 09:33:05'),
(2, 'tari anu anu', '6964c0132cd08.png', '2026-01-12 09:34:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `nama_penyewa` varchar(100) DEFAULT NULL,
  `no_invoice` varchar(20) DEFAULT NULL,
  `sanggar_id` int DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `lokasi` text,
  `status` enum('Tersedia','Dibooking','Lunas') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sanggar`
--

CREATE TABLE `sanggar` (
  `id` int NOT NULL,
  `nama_sanggar` varchar(100) DEFAULT NULL,
  `jenis_tari` varchar(100) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `sanggar`
--

INSERT INTO `sanggar` (`id`, `nama_sanggar`, `jenis_tari`, `harga`, `deskripsi`) VALUES
(1, 'JDK', 'jaipong', 200000, 'tradisional');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tarian`
--

CREATE TABLE `tarian` (
  `id` int NOT NULL,
  `nama_tarian` varchar(100) NOT NULL,
  `asal_daerah` varchar(100) DEFAULT NULL,
  `harga_dasar` int DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `minimal_penari` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `tarian`
--

INSERT INTO `tarian` (`id`, `nama_tarian`, `asal_daerah`, `harga_dasar`, `alamat`, `deskripsi`, `gambar`, `minimal_penari`) VALUES
(1, 'Tari Saman', 'Aceh', 100000, '-', 'Populer karena mengedepankan kerja sama tim dan ritme yang cepat.', '110126174634-Comment push github.png', 1),
(3, 'Tari Jaipong', 'Jawa Barat', 150000, '-', 'Mengingat lokasinya yang berada di pinggiran Jakarta/Jawa Barat, tari ini sering menjadi kurikulum dasar.', '', 1),
(4, 'Tari Piring', 'Sumatera Barat', 125000, '-', 'Sering dipentaskan dalam acara-acara besar sanggar.', '', 1),
(5, 'Tari Pendet', 'Bali', 175000, '-', 'Untuk melatih kelenturan tubuh dan ekspresi mata (seledet).', '', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `level` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `no_telp`, `level`, `created_at`) VALUES
(6, 'Administrator', 'admin2', '$2y$10$8C5L7O6M2I1K4N3P5R7S9U0V1W2X3Y4Z5A6B7C8D9E0F1G2H3I4J5', '08123', 'admin', '2026-01-11 23:01:29'),
(8, 'rio maryono', 'mario', 'aeb34368c5d53aee32431b5386f71c56', '008978', 'customer', '2026-01-11 23:16:37'),
(9, 'Administrator KJD', 'admin', '0192023a7bbd73250516f069df18b500', '08123456789', 'admin', '2026-01-11 23:28:51'),
(10, 'dewi kencana', 'dewi', 'fde0b737496c53bb85d07b31a02985a3', '0878657', 'customer', '2026-01-12 10:35:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sanggar_id` (`sanggar_id`);

--
-- Indeks untuk tabel `sanggar`
--
ALTER TABLE `sanggar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tarian`
--
ALTER TABLE `tarian`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `sanggar`
--
ALTER TABLE `sanggar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `tarian`
--
ALTER TABLE `tarian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
