<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') { exit; }

$id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'setuju') {
    // Jika disetujui, status_bayar jadi 'lunas'
    $query = "UPDATE bookings SET status_bayar = 'lunas' WHERE id = ?";
    $msg = "Pembayaran berhasil dikonfirmasi sebagai Lunas!";
} else {
    // Jika ditolak, status_bayar balik ke 'belum', dan bukti dihapus
    $query = "UPDATE bookings SET status_bayar = 'belum', bukti_pembayaran = NULL WHERE id = ?";
    $msg = "Pembayaran ditolak. Customer diminta mengunggah ulang.";
}

$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('$msg'); window.location='verifikasi_pembayaran.php';</script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>