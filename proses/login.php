<?php
session_start();
include "../config/koneksi.php"; 

// Pastikan data POST ada
if(isset($_POST['username']) && isset($_POST['password'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Kita gunakan MD5 agar sama dengan proses register
    $password_md5 = md5($password);

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND password = '$password_md5'");
    $data  = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['user_id']      = $data['id'];
        $_SESSION['nama_lengkap']  = $data['nama_lengkap']; 
        $_SESSION['level']         = $data['level'];
        
        if($data['level'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../customer/index.php");
        }
        exit();
    } else {
        // PERBAIKAN: Menggunakan JavaScript Alert agar tidak pindah halaman kosong
        echo "<script>
            alert('Gagal Login! Username atau Password salah.');
            window.location.href = '../index.php';
        </script>";
        exit();
    }
}
?>