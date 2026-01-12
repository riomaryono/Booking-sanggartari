<?php
include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $no_telp      = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $level        = $_POST['level'];
    
    // Enkripsi password agar aman
    $password     = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Gagal! Username sudah digunakan orang lain.'); window.history.back();</script>";
    } else {
        $query = "INSERT INTO users (nama_lengkap, username, password, no_telp, level) 
                  VALUES ('$nama_lengkap', '$username', '$password', '$no_telp', '$level')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Akun Customer Berhasil Dibuat!'); window.location='customer.php';</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>