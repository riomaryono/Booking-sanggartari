<?php
include "../config/koneksi.php";

if (isset($_POST['sanggar_id'])) {
    // Ambil data dari form
    $sanggar_id  = mysqli_real_escape_string($koneksi, $_POST['sanggar_id']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);

    // QUERY BARU: Kolom 'tanggal' sudah dihapus dari sini
    $query = "INSERT INTO jadwal (sanggar_id, jam_mulai, jam_selesai, status) 
              VALUES ('$sanggar_id', '$jam_mulai', '$jam_selesai', '$status')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Jadwal Berhasil Ditambahkan!'); window.location='jadwal.php';</script>";
    } else {
        // Jika ada error lain, akan muncul di sini
        echo "Error Database: " . mysqli_error($koneksi);
    }
} else {
    echo "Data tidak lengkap!";
}
?>