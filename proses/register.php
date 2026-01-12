<?php
include "../config/koneksi.php";

$nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
$user     = mysqli_real_escape_string($koneksi, $_POST['username']);
$telp     = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
$pass     = $_POST['password']; // Mengikuti cara sebelumnya (Plain Text/MD5 agar aman buat tes)
$level    = "customer";

// 1. Cek apakah username sudah dipakai
$cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$user'");

if (mysqli_num_rows($cek_user) > 0) {
    echo "<script>alert('Username sudah terdaftar! Gunakan yang lain.'); window.location='../index.php';</script>";
} else {
    // 2. Simpan ke database (Sesuai cara login terakhirmu, kita pakai MD5)
    $password_fix = md5($pass);
    
    $query = mysqli_query($koneksi, "INSERT INTO users (nama_lengkap, username, no_telp, password, level) 
                                     VALUES ('$nama', '$user', '$telp', '$password_fix', '$level')");

    if ($query) {
        echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='../index.php';</script>";
    } else {
        echo "Gagal daftar: " . mysqli_error($koneksi);
    }
}
?>