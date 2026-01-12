<?php
session_start();
include "../config/koneksi.php";
$conn = $koneksi;

if (isset($_POST['simpan'])) {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

    $update = mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', no_telp='$telp' WHERE id='$user_id'");

    if ($update) {
        // Update session jika diperlukan agar nama di sidebar langsung berubah
        $_SESSION['nama_lengkap'] = $nama;
        echo "<script>alert('Profil Berhasil Diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); window.location='profil.php';</script>";
    }
}
?>