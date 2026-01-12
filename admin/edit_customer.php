<?php
include "../config/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form modal
    $id           = $_POST['id'];
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $no_telp      = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $password     = $_POST['password'];

    // 1. Validasi: Cek apakah username sudah dipakai oleh orang lain (kecuali dirinya sendiri)
    $cek_username = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND id != '$id'");
    if (mysqli_num_rows($cek_username) > 0) {
        echo "<script>alert('Gagal! Username sudah digunakan orang lain.'); window.history.back();</script>";
        exit;
    }

    // 2. Logika Update: Cek apakah admin mengganti password atau tidak
    if (!empty($password)) {
        // Jika password diisi, enkripsi password baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                nama_lengkap = '$nama_lengkap', 
                username = '$username', 
                no_telp = '$no_telp', 
                password = '$hashed_password' 
                WHERE id = '$id'";
    } else {
        // Jika password kosong, update data selain password
        $sql = "UPDATE users SET 
                nama_lengkap = '$nama_lengkap', 
                username = '$username', 
                no_telp = '$no_telp' 
                WHERE id = '$id'";
    }

    // 3. Eksekusi Query
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Data customer berhasil diperbarui!'); window.location='customer.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    // Jika mencoba akses file ini langsung tanpa POST
    header("Location: customer.php");
}
?>