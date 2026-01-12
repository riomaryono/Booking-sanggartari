<?php
include "auth_admin.php";
include "../config/koneksi.php";

if (isset($_POST['simpan'])) {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

    $update = mysqli_query($koneksi, "UPDATE users SET nama_lengkap='$nama', no_telp='$telp' WHERE id='$user_id'");

    if ($update) {
        $_SESSION['nama_lengkap'] = $nama; // Update session nama agar di sidebar langsung berubah
        echo "<script>alert('Profil Admin Berhasil Diperbarui!'); window.location='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal update data.'); window.location='profil.php';</script>";
    }
}
?>