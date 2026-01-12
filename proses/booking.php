<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id         = $_SESSION['user_id'];
    $kategori_alamat = mysqli_real_escape_string($koneksi, $_POST['kategori_alamat']);
    $kategori_daerah = mysqli_real_escape_string($koneksi, $_POST['kategori_daerah']);
    $id_tarian       = $_POST['id_tarian'];
    $jenis_acara     = $_POST['jenis_acara'];
    $tanggal_booking = $_POST['tanggal_booking'];
    $jam_mulai       = $_POST['jam_mulai'];
    $total_harga     = $_POST['total_harga'];
    $durasi_jam      = $_POST['durasi_jam'];
    $jumlah_penari   = $_POST['jumlah_penari'];

    // Query menyesuaikan tabel bookings
    $query = "INSERT INTO bookings (
                user_id, kategori_alamat, kategori_daerah, id_tarian, 
                jenis_acara, tanggal_booking, jam_mulai, total_harga, 
                durasi_jam, jumlah_penari, status
              ) VALUES (
                '$user_id', '$kategori_alamat', '$kategori_daerah', '$id_tarian', 
                '$jenis_acara', '$tanggal_booking', '$jam_mulai', '$total_harga', 
                '$durasi_jam', '$jumlah_penari', 'pending'
              )";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pemesanan Berhasil!'); window.location='../customer/index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>