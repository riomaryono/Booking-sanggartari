<?php
session_start();
include "../config/koneksi.php";
$conn = $koneksi;

if (isset($_POST['update_pass'])) {
    $user_id = $_SESSION['user_id'];
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];

    // 1. Ambil password lama dari database
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id'");
    $data = mysqli_fetch_assoc($query);

    // 2. Verifikasi password lama dengan MD5
    // Kita ubah inputan user ke MD5 lalu bandingkan dengan yang ada di DB
    if (md5($pass_lama) == $data['password']) {
        
        // 3. Update ke password baru (juga di-MD5 sesuai sistem kamu)
        $md5_baru = md5($pass_baru);
        $update = mysqli_query($conn, "UPDATE users SET password = '$md5_baru' WHERE id = '$user_id'");

        if ($update) {
            echo "<script>alert('Password Berhasil Diganti!'); window.location='profil.php';</script>";
        } else {
            echo "<script>alert('Gagal mengupdate database.'); window.location='profil.php';</script>";
        }
    } else {
        // Jika hasil MD5 inputan tidak sama dengan MD5 di DB
        echo "<script>alert('Password lama Anda salah! (Sistem MD5)'); window.location='profil.php';</script>";
    }
}
?>